<?php

namespace Feadmin\Hooks;

use Feadmin\Facades\Preference;
use Illuminate\Support\Collection;

class PreferenceGroupHook
{
    protected string $lastNamespace;

    protected array $namespaces = [];

    public function namespace(string $id)
    {
        $this->lastNamespace = $id;
        $this->namespaces[$this->lastNamespace] ??= [];

        return $this;
    }

    public function group(string $id, string $title, float $position = null): self
    {
        $this->namespaces[$this->lastNamespace][$id] = [
            'title' => $title,
            'position' => is_null($position)
                ? (count($this->namespaces[$this->lastNamespace] ?? []) * 10)
                : $position,
        ];

        return $this;
    }

    public function get(): array
    {
        return collect($this->namespaces[$this->lastNamespace])
            ->sortBy('position')
            ->map(function ($preference, $namespace) {
                $rest = Preference::hook()->namespaces($this->lastNamespace)[$namespace] ?? [];

                return [
                    ...$preference,
                    ...$rest,
                ];
            })
            ->toArray();
    }

    public function dotted(): Collection
    {
        $map = function ($preferences, $namespace) {
            return collect($preferences)->mapWithKeys(
                fn ($preference, $key) => ["{$namespace}.{$key}" => $preference['title']]
            );
        };

        if (true) {
            return $map($this->get(), $this->lastNamespace);
        }

        return collect($this->get())
            ->map(fn ($preferences, $namespace) => $map($preferences, $namespace))
            ->collapse();
    }

    public function getField(string $group, string $key): ?array
    {
        $preferences = $this->get();

        if (is_null($preferences)) {
            return null;
        }

        $group = $preferences[$group] ?? null;

        if (is_null($group)) {
            return null;
        }

        return head(array_filter($group['fields'], function ($field) use ($key) {
            return $field['key'] === $key;
        }));
    }

    public function getFields(string $group): Collection
    {
        return collect($this->get()[$group]['fields'])->sortBy('position')->values();
    }
}
