<?php

namespace Feadmin\Managers;

use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Items\PreferenceItem;
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
        $this->preferences = Preference::query()->withTranslation()->get();
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
        $namespaces = $this->namespaces;

        if (is_null($namespace)) {
            return $namespaces;
        }

        return $namespaces[$namespace] ?? null;
    }

    public function add(PreferenceItem|array $field): self
    {
        if ($field instanceof PreferenceItem) {
            $field = $field->toArray();
        }

        $this->namespaces = [
            ...$this->namespaces,
            $this->currentNamespace => [
                ...$this->namespaces[$this->currentNamespace],
                $this->currentBag => [
                    ...$this->namespaces[$this->currentNamespace][$this->currentBag] ?? [],
                    'fields' => [
                        ...$this->namespaces[$this->currentNamespace][$this->currentBag]['fields'] ?? [],
                        [
                            'position' => count($this->namespaces[$this->currentNamespace][$this->currentBag]['fields'] ?? []) * 10,
                            'name' => "{$this->currentNamespace}::{$this->currentBag}->{$field['key']}",
                            ...$field
                        ],
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

        $field = $this->field($namespace, $bag, $key);
        $value = $found->value ?? $field['default'] ?? $default;

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
            $valueless = $found && ($field['type'] ?? null) === 'image';

            if (is_null($found) && filled($value)) {
                $saved[] = Preference::query()->create(array_filter([
                    'namespace' => $namespace,
                    'bag' => $bag,
                    'key' => $key,
                    'value' => $valueless ? null : $value,
                ]));

                continue;
            }

            if ($valueless) {
                $saved[] = $found;
                continue;
            }

            if ($found && blank($value)) {
                $found->delete();
                continue;
            }

            if ($found) {
                $found->update(['value' => $value]);
                $saved[] = $found;
            }
        }

        return $saved;
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

    public function field(string $namespace, string $bag, string $key): array|bool|null
    {
        $preferences = $this->namespaces($namespace);

        if (is_null($preferences)) {
            return null;
        }

        $bag = $preferences[$bag] ?? null;

        if (is_null($bag)) {
            return null;
        }

        return head(array_filter($bag['fields'], fn($field) => $field['key'] === $key));
    }

    public function fields(string $namespace, string $bag): Collection
    {
        return collect($this->namespaces($namespace)[$bag]['fields'])
            ->sortBy('position')
            ->values();
    }
}
