<?php

namespace Feadmin\Hooks;

use Feadmin\Facades\Feadmin;
use Feadmin\Facades\Preference;
use Illuminate\Support\Collection;

class PreferenceHook
{
    protected string $lastNamespace;

    protected string $lastGroup;

    protected bool $checkAbilities = true;

    protected array $namespaces = [];

    public function checkAbilities(): self
    {
        $this->checkAbilities = true;

        return $this;
    }

    public function ignoreAbilities(): self
    {
        $this->checkAbilities = false;

        return $this;
    }

    public function namespace(string $namespace): self
    {
        $this->lastNamespace = $namespace;
        $this->namespaces[$this->lastNamespace] ??= [];

        return $this;
    }

    public function group(string $id, string $title = null, float $position = null): self|array
    {
        $this->lastGroup = $id;

        if (is_null($title)) {
            return $this;
        }

        $this->namespaces[$this->lastNamespace][$this->lastGroup] = [
            'title' => $title,
            'fields' => [],
            'position' => is_null($position)
                ? (count($this->namespaces[$this->lastNamespace] ?? []) * 10)
                : $position,
        ];

        return $this;
    }

    public function namespaces(string $id = null): ?array
    {
        if ($this->checkAbilities) {
            $namespaces = collect($this->namespaces)
                ->map(function ($preferences, $namespace) {
                    return array_filter($preferences, function ($preference, $key) use ($namespace) {
                        return auth()->check() && auth()->user()->can("preference:{$namespace}.{$key}");
                    }, ARRAY_FILTER_USE_BOTH);
                })
                ->toArray();
        } else {
            $namespaces = $this->namespaces;
        }

        if (is_null($id)) {
            return $namespaces;
        }

        return $namespaces[$id] ?? null;
    }

    public function add(array $field): self
    {
        $this->namespaces = [
            ...$this->namespaces,
            $this->lastNamespace => [
                ...$this->namespaces[$this->lastNamespace],
                $this->lastGroup => [
                    ...$this->namespaces[$this->lastNamespace][$this->lastGroup] ?? [],
                    'fields' => [
                        ...$this->namespaces[$this->lastNamespace][$this->lastGroup]['fields'] ?? [],
                        [
                            'position' => count($this->namespaces[$this->lastNamespace][$this->lastGroup]['fields'] ?? []) * 10,
                            'name' => "{$this->lastNamespace}::{$this->lastGroup}__{$field['key']}",
                            ...$field
                        ],
                    ],
                ]
            ]
        ];

        return $this;
    }
}
