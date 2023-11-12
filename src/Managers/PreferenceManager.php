<?php

namespace Feadmin\Managers;

use Feadmin\Concerns\Fieldable;
use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Items\Field\FieldItem;
use Feadmin\Items\Field\GroupedFieldItem;
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

    public function add(FieldItem|RepeatedFieldItem|GroupedFieldItem $field): self
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
        [$found, $namespace, $bag, $key] = $this->find($rawKey);

        if (blank($key)) {
            return $default;
        }

        $field = $this->field($namespace, $bag, $key);

        if (($field['type'] ?? null) === FieldTypeEnum::REPEATED) {
            return $default;
        }

        $value = $field['translatable'] ? $found?->value : $found?->original_value;
        $value = $value ?? $field['default'] ?? $default;

        /** @var ?FieldTypeEnum $type */
        $type = $field['type'] ?? null;

        if ($type?->isUploadable()) {
            return $found?->getFirstMediaUrl(conversionName: 'lg') ?? '';
        }

        if ($type?->isHtmlable()) {
            return new HtmlString($value);
        }

        return $value;
    }

    public function set(array $data): array
    {
        $saved = [];

        foreach ($data as $rawKey => $value) {
            [$found, $namespace, $bag, $key] = $this->find($rawKey);
            $field = $this->field($namespace, $bag, $key);

            if ($field['type'] === FieldTypeEnum::REPEATED) {
                $saved = array_merge($saved, $this->setRepeatedField($field, $value));
                continue;
            }

            $valueless = $found && ($field['type'] ?? null)?->isValueless();
            $value = $valueless ? null : $value;

            $valueKey = $field['translatable'] ? 'value' : 'original_value';

            $saved[] = $this->setSingleField($found, $namespace, $bag, $key, $value, $valueKey, $valueless);
        }

        return $saved;
    }

    protected function setRepeatedField(Fieldable $field, $value): array
    {
        $saved = [];

        foreach ($field['fields'] as $repeatedFieldKey => $repeatedField) {
            $repeatedKey = sprintf(
                '%s.%d.%s',
                $field['name'],
                $repeatedFieldKey,
                $repeatedField['name']
            );

            $repeatedValue = $value[$repeatedFieldKey][$repeatedField['name']] ?? null;

            $saved = array_merge($saved, $this->set([$repeatedKey => $repeatedValue]));
        }

        return $saved;
    }

    protected function setSingleField($found, string $namespace, string $bag, string $key, $value, string $valueKey, bool $valueless): ?Preference
    {
        if (is_null($found) && filled($value)) {
            return $this->createNewPreference($namespace, $bag, $key, $value, $valueKey);
        }

        if ($valueless) {
            return $found;
        }

        if ($found && blank($value)) {
            $found->delete();
            return null;
        }

        if ($found) {
            $found->update([$valueKey => $value]);
            return $found;
        }

        return null;
    }

    protected function createNewPreference(string $namespace, string $bag, string $key, $value, string $valueKey): Preference
    {
        /** @var Preference $preference */
        $preference = Preference::query()->create(array_filter([
            'namespace' => $namespace,
            'bag' => $bag,
            'key' => $key,
            $valueKey => $value,
        ]));

        return $preference;
    }

    public function find(string $rawKey): array
    {
        if (!str_contains($rawKey, '::')) {
            $rawKey = "default::{$rawKey}";
        }

        [$namespace, $bagAndKey] = explode('::', $rawKey);
        [$bag, $key] = explode('->', $bagAndKey, 2);

        $foundPreference = $this->preferences
            ->where('namespace', $namespace)
            ->where('bag', $bag)
            ->where('key', $key)
            ->first();

        return [$foundPreference, $namespace, $bag, $key];
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
            $this->processFieldForValidation($field, $rules, $attributes);
        }

        return compact('rules', 'attributes');
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

        $field->name(implode('.', [$repeatedField['name'], $fieldIndex, $fieldKey]));

        return $field;
    }

    protected function processFieldForValidation(Fieldable $field, array &$rules, array &$attributes): void
    {
        if ($field['type'] === FieldTypeEnum::REPEATED) {
            $this->processRepeatedFieldForValidation($field, $rules, $attributes);
            return;
        }

        $attributes[$field['name']] = $field['label'];

        if ($field['type']?->isEditable() && isset($field['rules'])) {
            $rules[$field['name']] = $field['rules'];
        }
    }

    protected function processRepeatedFieldForValidation(Fieldable $field, array &$rules, array &$attributes): void
    {
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
