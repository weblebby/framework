<?php

namespace Feadmin\Managers;

use Exception;
use Feadmin\Concerns\Fieldable;
use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Items\Field\FieldItem;
use Feadmin\Items\Field\RepeatedFieldItem;
use Feadmin\Models\Preference;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class PreferenceManager
{
    protected string $currentNamespace;

    protected string $currentBag;

    protected array $namespaces = [];

    protected Collection $preferences;

    public function __construct()
    {
        $this->loadPreferences();
    }

    public function loadPreferences(): self
    {
        $this->preferences = Preference::query()->withTranslation()->get();

        return $this;
    }

    public function create(string $namespace, string $bag): self
    {
        return $this->withNamespace($namespace)->withBag($bag);
    }

    public function withNamespace(string $namespace): self
    {
        $this->currentNamespace = $namespace;
        $this->namespaces[$this->currentNamespace] ??= [];

        return $this;
    }

    public function withBag(string $bag): self
    {
        $this->currentBag = $bag;
        $this->namespaces[$this->currentNamespace][$this->currentBag] ??= [];

        return $this;
    }

    public function namespaces(string $namespace = null): ?array
    {
        return is_null($namespace) ? $this->namespaces : $this->namespaces[$namespace] ?? null;
    }

    public function add(Fieldable $field): self
    {
        if ($field instanceof FieldItem || $field instanceof RepeatedFieldItem) {
            $field->name("{$this->currentNamespace}::{$this->currentBag}->{$field['key']}");
        }

        if (!is_array($field)) {
            $field->position(count($this->getCurrentFields()) * 10);
        }

        $this->namespaces = [
            ...$this->namespaces,
            $this->currentNamespace => [
                ...$this->namespaces[$this->currentNamespace],
                $this->currentBag => [
                    ...$this->namespaces[$this->currentNamespace][$this->currentBag] ?? [],
                    'fields' => [
                        ...$this->namespaces[$this->currentNamespace][$this->currentBag]['fields'] ?? [],
                        $field,
                    ],
                ]
            ]
        ];

        return $this;
    }

    public function addMany(array $fields): self
    {
        foreach ($fields as $field) {
            $this->add($field);
        }

        return $this;
    }

    public function get(string $rawKey, mixed $default = null): mixed
    {
        [$preference, $field] = $this->find($rawKey);

        if ($preference instanceof Collection) {
            return $preference->map(
                fn(Collection $preferences) => $preferences->map(
                    fn(array $fieldAndPreference) => $this->getValue(
                        $fieldAndPreference['preference'],
                        $fieldAndPreference['field'],
                    )
                )->toArray()
            )->toArray();
        }

        return $this->getValue($preference, $field, $default);
    }

    public function set(array $data): array
    {
        $saved = [];

        foreach ($data as $rawKey => $value) {
            [$preference, $field] = $this->find($rawKey);

            if ($field['type'] === FieldTypeEnum::REPEATED) {
                $saved = array_merge($saved, $this->setRepeatedField($preference, $field, $value));
                continue;
            }

            $saved[] = $this->setSingleField($preference, $field, $value);
        }

        return array_filter($saved);
    }

    public function find(string $rawKey): array
    {
        [$namespace, $bag, $key] = $this->parseRawKey($rawKey);
        $field = $this->field($namespace, $bag, $key);

        if (($field['type'] ?? null) === FieldTypeEnum::REPEATED) {
            $preferences = $this->preferences
                ->where('namespace', $namespace)
                ->where('bag', $bag)
                ->where(fn(Preference $preference) => str_contains($preference->key, $key))
                ->groupBy(fn(Preference $preference) => explode('.', $preference->key)[1])
                ->map(function (Collection $preferences) {
                    return $preferences->mapWithKeys(function (Preference $preference) {
                        $field = $this->field(
                            $preference->namespace,
                            $preference->bag,
                            $preference->key
                        );

                        $key = explode('.', $preference->key)[2];

                        return [$key => compact('preference', 'field')];
                    });
                });

            return [$preferences, $field];
        }

        $foundPreference = $this->preferences
            ->where('namespace', $namespace)
            ->where('bag', $bag)
            ->where('key', $key)
            ->first();

        return [$foundPreference, $field];
    }

    public function field(string $namespace, string $bag, string $key): ?Fieldable
    {
        $preferences = $this->namespaces($namespace);

        if (is_null($preferences)) {
            return null;
        }

        $bag = $preferences[$bag] ?? null;

        if (is_null($bag)) {
            return null;
        }

        if (str_contains($key, '.')) {
            return $this->getRepeatedField($bag, $key);
        }

        return $this->getField($bag, $key);
    }

    public function fields(string $namespace, string $bag): Collection
    {
        return collect($this->namespaces($namespace)[$bag]['fields'])
            ->sortBy('position')
            ->values();
    }

    public function fieldsForValidation(string $namespace, string $bag): array
    {
        $rules = [];
        $attributes = [];

        foreach ($this->fields($namespace, $bag) as $field) {
            if ($field['type'] === FieldTypeEnum::REPEATED) {
                $this->processRepeatedFieldForValidation($field, $rules, $attributes);
                continue;
            }

            $attributes[$field['name']] = $field['label'];

            if ($field['type']?->isEditable() && isset($field['rules'])) {
                $rules[$field['name']] = $field['rules'];
            }
        }

        return compact('rules', 'attributes');
    }

    protected function setRepeatedField(Collection $preferences, Fieldable $field, ?array $rows): array
    {
        $saved = [];

        $preferences->each(function (Collection $preferences, string $index) use ($rows, $field) {
            $row = $rows[$index] ?? null;

            if (is_null($row)) {
                $preferences->each(function (array $preferenceAndField) {
                    $preferenceAndField['preference']->delete();
                });
            }
        });

        foreach ($rows ?? [] as $index => $row) {
            foreach ($row as $key => $value) {
                $rawKey = sprintf(
                    '%s.%d.%s',
                    $field['name'],
                    $index,
                    $key
                );

                $saved = array_merge($saved, $this->set([$rawKey => $value]));
            }
        }

        return $saved;
    }

    protected function setSingleField(?Preference $preference, Fieldable $field, mixed $value): ?Preference
    {
        $valueless = $preference && ($field['type'] ?? null)?->isValueless();
        $value = $valueless ? null : $value;
        $valueKey = $field['translatable'] ? 'value' : 'original_value';

        if (is_null($preference) && filled($value)) {
            return $this->createNewPreference($field, [$valueKey => $value]);
        }

        if ($valueless) {
            return $preference;
        }

        if ($preference && blank($value)) {
            $preference->delete();
            return null;
        }

        if ($preference) {
            $preference->update([$valueKey => $value]);
            return $preference;
        }

        return null;
    }

    protected function createNewPreference(Fieldable $field, array $data): Preference
    {
        [$namespace, $bag, $key] = $this->parseRawKey($field['name']);

        /** @var Preference $preference */
        $preference = Preference::query()->create(array_filter([
            'namespace' => $namespace,
            'bag' => $bag,
            'key' => $key,
            ...$data,
        ]));

        return $preference;
    }

    protected function getValue(?Preference $preference, ?Fieldable $field, mixed $default = null): mixed
    {
        if (blank($field['key'] ?? null) || ($field['type'] ?? null) === FieldTypeEnum::REPEATED) {
            return $default;
        }

        $value = $field['translatable'] ? $preference?->value : $preference?->original_value;
        $value = $value ?? $field['default'] ?? $default;

        /** @var ?FieldTypeEnum $type */
        $type = $field['type'] ?? null;

        if ($type?->isUploadable()) {
            return $preference?->getFirstMediaUrl(conversionName: 'lg') ?? '';
        }

        if ($type?->isHtmlable()) {
            return new HtmlString($value);
        }

        return $value;
    }

    protected function parseRawKey(string $rawKey): array
    {
        if (!str_contains($rawKey, '->')) {
            throw new Exception(
                sprintf('Invalid preference key [%s]. Please use the following format: namespace::bag->key', $rawKey)
            );
        }

        if (!str_contains($rawKey, '::')) {
            $rawKey = "default::{$rawKey}";
        }

        [$namespace, $bagAndKey] = explode('::', $rawKey);
        [$bag, $key] = explode('->', $bagAndKey, 2);

        return [$namespace, $bag, $key];
    }

    protected function getField(array $bag, string $key): ?Fieldable
    {
        $field = head(array_filter($bag['fields'], fn($field) => $field['key'] === $key));

        if ($field === false) {
            return null;
        }

        return $field;
    }

    protected function getRepeatedField(array $bag, string $key): ?Fieldable
    {
        [$repeatedKey, $fieldIndex, $fieldKey] = explode('.', $key);
        $repeatedField = head(array_filter($bag['fields'], fn($field) => $field['key'] === $repeatedKey));

        if ($repeatedField === false) {
            return null;
        }

        $field = head(array_filter($repeatedField['fields'], fn($field) => $field['key'] === $fieldKey));

        if ($field === false) {
            return null;
        }

        return (clone $field)->name(
            implode('.', [$repeatedField['name'], $fieldIndex, $fieldKey])
        );
    }

    protected function processRepeatedFieldForValidation(Fieldable $field, array &$rules, array &$attributes): void
    {
        $rules[$field['name']] = ['nullable', 'array', "max:{$field['max']}"];

        foreach ($field['field_rules'] as $key => $rule) {
            $rules[$key] = $rule;
        }

        foreach ($field['field_labels'] as $key => $label) {
            $attributes[$key] = $label;
        }
    }

    protected function getCurrentFields(): array
    {
        return $this->namespaces[$this->currentNamespace][$this->currentBag]['fields'] ?? [];
    }
}
