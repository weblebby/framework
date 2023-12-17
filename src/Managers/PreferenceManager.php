<?php

namespace Feadmin\Managers;

use Exception;
use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Items\Field\Contracts\FieldInterface;
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

    public function add(FieldInterface $field): self
    {
        $this->setFieldName($field);

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
                ],
            ],
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
            $map = function (array $preferenceAndField) use ($default, &$map) {
                if (!isset($preferenceAndField['preference']) || !isset($preferenceAndField['field'])) {
                    foreach ($preferenceAndField as $key => $value) {
                        $preferenceAndField[$key] = $map($value);
                    }

                    return $preferenceAndField;
                }

                return $this->getFieldValue(
                    $preferenceAndField['preference'],
                    $preferenceAndField['field'],
                    $default,
                );
            };

            return $preference->map($map)->toArray();
        }

        return $this->getFieldValue($preference, $field, $default);
    }

    public function set(array $data): array
    {
        $saved = [];

        foreach ($data as $rawKey => $value) {
            [$preference, $field] = $this->find($rawKey);
            $field = (clone $field)->name($rawKey);

            if ($field['type'] === FieldTypeEnum::REPEATED) {
                $saved = array_merge($saved, $this->setRepeatedField($preference, $field, $value));

                continue;
            }

            $saved[] = $this->setField($preference, $field, $value);
        }

        return array_filter($saved);
    }

    public function find(string $rawKey): array
    {
        [$namespace, $bag, $key] = $this->parseRawKey($rawKey);
        $field = $this->field($rawKey);

        if (($field['type'] ?? null) === FieldTypeEnum::REPEATED) {
            return $this->findFromRepeatedPreference($field, $rawKey);
        }

        $foundPreference = $this->preferences
            ->where('namespace', $namespace)
            ->where('bag', $bag)
            ->where('key', $key)
            ->first();

        return [$foundPreference, $field];
    }

    protected function findFromRepeatedPreference(FieldInterface $field, string $rawKey): array
    {
        [$namespace, $bag, $key] = $this->parseRawKey($rawKey);

        $preferences = $this->preferences
            ->where('namespace', $namespace)
            ->where('bag', $bag)
            ->where(fn(Preference $preference) => str_contains($preference->key, $key))
            ->mapWithKeys(function (Preference $preference) use ($rawKey) {
                $field = $this->field($preference->getFullKey());

                $key = str_replace($rawKey, '', $preference->getFullKey());
                $key = ltrim($key, '.');

                return [$key => compact('preference', 'field')];
            })
            ->undot();

        return [$preferences, $field];
    }

    public function field(string $rawKey): ?FieldInterface
    {
        [$namespace, $bag, $key] = $this->parseRawKey($rawKey);

        $preferences = $this->namespaces($namespace);

        if (is_null($preferences)) {
            return null;
        }

        $bag = $preferences[$bag] ?? null;

        if (is_null($bag)) {
            return null;
        }

        if (str_contains($key, '.')) {
            return $this->findFromRepeatedFieldByKey($bag['fields'], $rawKey);
        }

        return $this->findFieldByKey($bag['fields'], $key);
    }

    public function fields(string $namespace, string $bag): Collection
    {
        return collect($this->namespaces($namespace)[$bag]['fields'])
            ->sortBy('position')
            ->values();
    }

    protected function createNewPreference(FieldInterface $field, array $data): Preference
    {
        [$namespace, $bag, $key] = $this->parseRawKey($field['name']);

        /** @var Preference $preference */
        $preference = Preference::query()->create(array_filter([
            'namespace' => $namespace,
            'bag' => $bag,
            'key' => $key,
            ...$data,
        ], fn($value) => filled($value)));

        return $preference;
    }

    protected function getFieldValue(?Preference $preference, ?FieldInterface $field, mixed $default = null): mixed
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

    protected function findFieldByKey(array $fields, string $key): ?FieldInterface
    {
        $field = head(array_filter($fields, fn($field) => $field['key'] === $key));

        if ($field === false) {
            return null;
        }

        return $field;
    }

    protected function findFromRepeatedFieldByKey(array $fields, string $rawKey): ?FieldInterface
    {
        foreach ($fields as $field) {
            $rawKeyToWildcard = preg_replace('/\.\d+\./', '.*.', $rawKey);

            if (str_contains($rawKeyToWildcard, $field['name'])) {
                $index = str_replace($field['name'] . '.', '', $rawKeyToWildcard);

                if (is_numeric($index)) {
                    return $field;
                }

                if ($rawKeyToWildcard !== $field['name'] && $field instanceof RepeatedFieldItem) {
                    return $this->findFromRepeatedFieldByKey($field['fields'], $rawKey);
                }

                return $field;
            }
        }

        return null;
    }

    protected function getCurrentFields(): array
    {
        return $this->namespaces[$this->currentNamespace][$this->currentBag]['fields'] ?? [];
    }

    protected function addMissingNamespaceToRawKey(string $rawKey): string
    {
        if (!str_contains($rawKey, '::')) {
            $rawKey = "{$this->currentNamespace}::{$rawKey}";
        }

        return $rawKey;
    }

    protected function parseRawKey(string $rawKey): array
    {
        if (!str_contains($rawKey, '->')) {
            throw new Exception(
                sprintf('Invalid preference key [%s]. Please use the following format: namespace::bag->key', $rawKey)
            );
        }

        $rawKey = $this->addMissingNamespaceToRawKey($rawKey);

        [$namespace, $bagAndKey] = explode('::', $rawKey);
        [$bag, $key] = explode('->', $bagAndKey, 2);

        return [$namespace, $bag, $key];
    }

    protected function setFieldName(FieldInterface $field, string $parentKey = null): void
    {
        if (method_exists($field, 'name')) {
            if (is_null($parentKey)) {
                $field->name("{$this->currentNamespace}::{$this->currentBag}->{$field['key']}");
            } else {
                $field->name("{$parentKey}.*.{$field['key']}");
            }
        }

        if (method_exists($field, 'fields')) {
            collect($field['fields'])->each(function (FieldInterface $child) use ($field) {
                $this->setFieldName($child, $field['name']);
            });
        }
    }

    protected function setRepeatedField(Collection $preferences, FieldInterface $field, array|null $rows): array
    {
        $saved = [];

        $preferences->each(function (array $preferences, string $index) use ($rows) {
            $row = $rows[$index] ?? null;

            if (is_null($row)) {
                $map = function (array $preferenceAndField) use (&$map) {
                    if (!isset($preferenceAndField['preference']) || !isset($preferenceAndField['field'])) {
                        foreach ($preferenceAndField as $key => $value) {
                            $preferenceAndField[$key] = $map($value);
                        }

                        return $preferenceAndField;
                    }

                    return $preferenceAndField['preference'];
                };

                collect($preferences)->map($map)->dot()->each->delete();
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

    protected function setField(?Preference $preference, FieldInterface $field, mixed $value): ?Preference
    {
        $valueColumn = $field['translatable'] && !$field['type']->isValueless() ? 'value' : 'original_value';

        if ($field['type']->isUploadable()) {
            $uploadedFile = $value;
            $value = true;
        } elseif ($field['type']->isValueless()) {
            $value = null;
        }

        if (is_null($preference) && filled($value)) {
            $preference = $this->createNewPreference($field, [$valueColumn => $value]);

            if ($field['type']->isUploadable()) {
                $preference->addMedia($uploadedFile)->toMediaCollection();
            }

            return $preference;
        }

        if ($preference && blank($value)) {
            $preference->delete();

            return null;
        }

        if ($preference) {
            if ($field['type']->isUploadable()) {
                $preference->addMedia($uploadedFile)->toMediaCollection();
            } else {
                $preference->update([$valueColumn => $value]);
            }

            return $preference;
        }

        return null;
    }
}
