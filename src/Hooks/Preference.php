<?php

namespace Feadmin\Hooks;

use Feadmin\Items\PreferenceItem;
use Illuminate\Support\Collection;

class Preference
{
    protected string $lastNamespace;

    protected string $lastBag;

    protected array $namespaces = [];

    public function namespace(string $namespace): self
    {
        $this->lastNamespace = $namespace;
        $this->namespaces[$this->lastNamespace] ??= [];

        return $this;
    }

    public function bag(string $bag): self
    {
        $this->lastBag = $bag;
        $this->namespaces[$this->lastNamespace][$this->lastBag] ??= [];

        return $this;
    }

    public function create(string $namespace, string $bag): self
    {
        return $this->namespace($namespace)->bag($bag);
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
            $field = $field->get();
        }

        $this->namespaces = [
            ...$this->namespaces,
            $this->lastNamespace => [
                ...$this->namespaces[$this->lastNamespace],
                $this->lastBag => [
                    ...$this->namespaces[$this->lastNamespace][$this->lastBag] ?? [],
                    'fields' => [
                        ...$this->namespaces[$this->lastNamespace][$this->lastBag]['fields'] ?? [],
                        [
                            'position' => count($this->namespaces[$this->lastNamespace][$this->lastBag]['fields'] ?? []) * 10,
                            'name' => "{$this->lastNamespace}::{$this->lastBag}->{$field['key']}",
                            ...$field
                        ],
                    ],
                ]
            ]
        ];

        return $this;
    }

    public function addMany(array $items): self
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
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

        return head(array_filter($bag['fields'], function ($field) use ($key) {
            return $field['key'] === $key;
        }));
    }

    public function fields(string $namespace, string $bag): Collection
    {
        return collect($this->namespaces($namespace)[$bag]['fields'])
            ->sortBy('position')
            ->values();
    }
}
