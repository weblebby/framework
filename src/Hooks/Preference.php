<?php

namespace Feadmin\Hooks;

use Illuminate\Support\Collection;

class Preference
{
    protected string $lastNamespace;

    protected string $lastBag;

    // protected bool $checkAbilities = true;

    protected array $namespaces = [];

    // public function checkAbilities(): self
    // {
    //     $this->checkAbilities = true;

    //     return $this;
    // }

    // public function ignoreAbilities(): self
    // {
    //     $this->checkAbilities = false;

    //     return $this;
    // }

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
        $this->namespace($namespace)->bag($bag);

        return $this;
    }

    // public function bag(string $id, string $title = null, float $position = null): self|array
    // {
    //     $this->lastBag = $id;

    //     if (is_null($title)) {
    //         return $this;
    //     }

    //     $this->namespaces[$this->lastNamespace][$this->lastBag] = [
    //         'title' => $title,
    //         'fields' => [],
    //         'position' => is_null($position)
    //             ? (count($this->namespaces[$this->lastNamespace] ?? []) * 10)
    //             : $position,
    //     ];

    //     return $this;
    // }

    public function namespaces(string $namespace = null): ?array
    {
        // if ($this->checkAbilities) {
        //     $namespaces = collect($this->namespaces)
        //         ->map(function ($preferences, $namespace) {
        //             return array_filter($preferences, function ($preference, $key) use ($namespace) {
        //                 return auth()->check() && auth()->user()->can("preference:{$namespace}.{$key}");
        //             }, ARRAY_FILTER_USE_BOTH);
        //         })
        //         ->toArray();
        // } else {
        //     $namespaces = $this->namespaces;
        // }

        $namespaces = $this->namespaces;

        if (is_null($namespace)) {
            return $namespaces;
        }

        return $namespaces[$namespace] ?? null;
    }

    public function add(array $field): self
    {
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

    public function field(string $bag, string $key): ?array
    {
        $preferences = $this->namespaces($this->lastNamespace);

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

    public function fields(string $bag): Collection
    {
        return collect($this->namespaces($this->lastNamespace)[$bag]['fields'])
            ->sortBy('position')
            ->values();
    }
}
