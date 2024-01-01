<?php

namespace Feadmin\Managers;

use Exception;
use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Items\Field\Collections\FieldCollection;
use Feadmin\Items\Field\Contracts\FieldInterface;
use Feadmin\Items\Field\Contracts\HasChildFieldInterface;
use Feadmin\Items\Field\FieldValueItem;
use Feadmin\Items\Field\RepeatedFieldItem;
use Feadmin\Models\Metafield;
use Feadmin\Models\Preference;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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
        $this->preferences = Preference::query()->with('metafields')->get();

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

    /**
     * @throws Exception
     */
    public function get(string $rawKey, mixed $default = null): mixed
    {
        [$metafield, $field] = $this->find($rawKey);

        if ($rawKey === 'default::general->settings') {
            dd($metafield, $field, $rawKey);
        }

        if ($metafield instanceof Collection) {
            $map = function (array $metafieldAndField) use ($metafield, $default, &$map) {
                if (!isset($metafieldAndField['metafield']) || !isset($metafieldAndField['field'])) {
                    foreach ($metafieldAndField as $key => $value) {
                        $metafieldAndField[$key] = $map($value);
                    }

                    return $metafieldAndField;
                }

                return $this->getFieldValue(
                    $metafieldAndField['metafield']->metafieldable,
                    $metafieldAndField['field'],
                    $default,
                );
            };

            return $metafield->map($map)->toArray();
        }

        return $this->getFieldValue($metafield?->metafieldable, $field, $default);
    }

    /**
     * @throws Exception
     */
    public function set(array $data, string $locale = null): array
    {
        $saved = [];

        foreach ($data as $rawKey => $value) {
            [$_, $_, $key] = $this->parseRawKey($rawKey);
            [$_, $field] = $this->find($rawKey);

            $fieldValue = $value instanceof FieldValueItem ? $value : new FieldValueItem($field, $value);

            $preference ??= $this->getOrCreatePreference($field);
            $saved[] = $preference->setMetafieldWithSchema($key, $fieldValue, $locale);
        }

        return array_filter($saved);
    }

    /**
     * @throws Exception
     */
    public function find(string $rawKey): array
    {
        [$namespace, $bag, $key] = $this->parseRawKey($rawKey);
        $field = $this->field($rawKey);

        if ($field instanceof HasChildFieldInterface) {
            return $this->findFromRepeatedPreference($field, $rawKey);
        }

        /** @var Preference $foundPreference */
        $foundPreference = $this->preferences
            ->where('namespace', $namespace)
            ->where('bag', $bag)
            ->first();

        /** @var Metafield $foundMetafield */
        $foundMetafield = $foundPreference?->metafields->where('key', $key)->first();
        $foundMetafield?->setRelation('preference', $foundPreference);

        return [$foundMetafield, $field];
    }

    /**
     * @throws Exception
     */
    protected function findFromRepeatedPreference(HasChildFieldInterface $field, string $rawKey): array
    {
        [$namespace, $bag, $key] = $this->parseRawKey($rawKey);

        $metafields = $this->preferences
            ->where('namespace', $namespace)
            ->where('bag', $bag)
            ->map(fn(Preference $preference) => $preference->metafields)
            ->flatten()
            ->filter(fn(Metafield $metafield) => str_contains($metafield->key, $key))
            ->mapWithKeys(function (Metafield $metafield) {
                $rawKey = "fields.{$metafield->metafieldable->getNamespaceAndBag()}->{$metafield->key}";
                $field = $this->field($rawKey);

                return [$metafield->key => compact('metafield', 'field')];
            })
            ->undot();

        return [$metafields, $field];
    }

    /**
     * @throws Exception
     */
    public function field(string $rawKey): ?FieldInterface
    {
        if (!str_starts_with($rawKey, 'fields.')) {
            $rawKey = "fields.{$rawKey}";
        }

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

    protected function getOrCreatePreference(FieldInterface $field): Preference
    {
        [$namespace, $bag] = $this->parseRawKey($field['name']);

        /** @var Preference $preference */
        $preference = Preference::query()->firstOrCreate([
            'namespace' => $namespace,
            'bag' => $bag,
        ]);

        return $preference;
    }

    protected function getFieldValue(?Preference $preference, ?FieldInterface $field, mixed $default = null): mixed
    {
        $values = $preference?->getMetafieldValues(new FieldCollection([$field]));

        if (is_null($values)) {
            return $default;
        }

        return head($values) ?: $default;
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

                if (is_numeric($index) || $rawKeyToWildcard === $field['name']) {
                    return $field;
                }

                if ($field instanceof HasChildFieldInterface) {
                    if ($foundField = $this->findFromRepeatedFieldByKey($field['fields'], $rawKey)) {
                        return $foundField;
                    }
                }
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

    /**
     * @throws Exception
     */
    protected function parseRawKey(string $rawKey): array
    {
        if (!str_contains($rawKey, '->')) {
            throw new Exception(
                sprintf('Invalid preference key [%s]. Please use the following format: namespace::bag->key', $rawKey)
            );
        }

        $rawKey = Str::replaceFirst('fields.', '', $rawKey);
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
            } elseif ($field['key']) {
                $field->name("{$parentKey}.*.{$field['key']}");
            }
        }

        if ($field instanceof HasChildFieldInterface) {
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
