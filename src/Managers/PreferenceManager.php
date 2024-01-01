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
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
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
        $this->preferences = Preference::query()
            ->with(['metafields' => fn(MorphMany $builder) => $builder->oldest('key')])
            ->get();

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
        if (str_starts_with($rawKey, 'fields.')) {
            $rawKey = Str::replaceFirst('fields.', '', $rawKey);
        }

        [$metafield, $field] = $this->find($rawKey);

        if ($metafield instanceof Collection) {
            $map = function (array $metafieldAndField) use ($metafield, $default, &$map) {
                if (isset($metafieldAndField['metafield']) && isset($metafieldAndField['field'])) {
                    return $metafieldAndField['metafield']->toValue($metafieldAndField['field'], $default);
                }

                foreach ($metafieldAndField as $key => $value) {
                    $metafieldAndField[$key] = $map($value);
                }

                return $metafieldAndField;
            };

            return $metafield->map($map)->toArray();
        }

        return $metafield?->toValue($field, $default) ?? $default;
    }

    /**
     * @throws Exception
     */
    public function set(array $data, string $locale = null, array $options = []): array
    {
        $saved = [];

        $groups = collect($data)->groupBy(function ($value, $rawKey) {
            [$namespace, $bag] = $this->parseRawKey($rawKey);
            return "{$namespace}::{$bag}";
        }, preserveKeys: true);

        foreach ($groups as $namespaceAndBag => $fields) {
            [$namespace, $bag] = explode('::', $namespaceAndBag);

            /** @var Preference $preference */
            $preference = Preference::query()->firstOrCreate(
                compact('namespace', 'bag')
            );

            $fields = $fields->mapWithKeys(function ($value, $rawKey) {
                [$_, $_, $key] = $this->parseRawKey($rawKey);

                if ($value instanceof FieldValueItem) {
                    return [$key => $value];
                }

                return [$key => new FieldValueItem($this->field($rawKey), $value)];
            });

            if (is_array($options['deleted_fields'] ?? null)) {
                $options['deleted_fields'] = collect($options['deleted_fields'])
                    ->map(function ($field) use ($preference) {
                        try {
                            [$_, $_, $key] = $this->parseRawKey($field);
                        } catch (Exception) {
                            $key = $field;
                        }

                        return $key;
                    })
                    ->toArray();

                $preference->deleteMetafields(startsWith: $options['deleted_fields']);
            }

            $preference->setMetafieldWithSchema($fields, locale: $locale);

            if (is_array($options['reordered_fields'] ?? null)) {
                $options['reordered_fields'] = collect($options['reordered_fields'])
                    ->mapWithKeys(function ($newFullKey, $oldFullKey) use ($preference) {
                        try {
                            [$_, $_, $newKey] = $this->parseRawKey($newFullKey);
                            [$_, $_, $oldKey] = $this->parseRawKey($oldFullKey);
                        } catch (Exception) {
                            $newKey = $newFullKey;
                            $oldKey = $oldFullKey;
                        }

                        return [$oldKey => $newKey];
                    })
                    ->toArray();

                $preference->reorderMetafields($options['reordered_fields']);
            }

            $preference->resetMetafieldKeys();
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
            ->mapWithKeys(function (Metafield $metafield) use ($rawKey) {
                $fullKey = "{$metafield->metafieldable->getNamespaceAndBag()}->{$metafield->key}";

                $field = $this->field($fullKey);

                $key = str_replace($rawKey, '', $fullKey);
                $key = ltrim($key, '.');

                return [$key => compact('metafield', 'field')];
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
}
