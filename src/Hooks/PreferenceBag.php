<?php

namespace Feadmin\Hooks;

use Feadmin\Facades\Preference;
use Illuminate\Support\Collection;

class PreferenceBag
{
    protected string $lastNamespace;

    protected array $namespaces = [];

    public function namespace(string $id): self
    {
        $this->lastNamespace = $id;
        $this->namespaces[$this->lastNamespace] ??= [];

        return $this;
    }

    public function bag(string $id, string $title, float $position = null): self
    {
        $this->namespaces[$this->lastNamespace][$id] = [
            'title' => $title,
            'position' => is_null($position)
                ? (count($this->namespaces[$this->lastNamespace] ?? []) * 10)
                : $position,
            'permission' => "preference:{$id}.{$this->lastNamespace}",
        ];

        return $this;
    }

    public function field(string $bag, string $key): ?array
    {
        $preferences = $this->get();

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
        return collect($this->get()[$bag]['fields'] ?? [])
            ->sortBy('position')
            ->values();
    }

    public function getAll(): array
    {
        return collect($this->namespaces)
            ->map(function ($preferences) {
                return array_filter(
                    $preferences,
                    fn ($item) => auth()->check() && auth()->user()->can($item['permission'])
                );
            })
            ->toArray();
    }

    public function get(): array
    {
        return collect($this->getAll()[$this->lastNamespace])
            ->sortBy('position')
            ->map(function ($preference, $namespace) {
                $rest = Preference::hook()->namespaces($this->lastNamespace)[$namespace] ?? null;

                if (is_null($rest)) {
                    return false;
                }

                return [
                    ...$preference,
                    ...$rest,
                ];
            })
            ->filter()
            ->toArray();
    }

    public function toDotted(bool $all = false): Collection
    {
        $map = function ($preferences, $namespace) {
            return collect($preferences)->mapWithKeys(
                fn ($preference, $key) => ["{$namespace}.{$key}" => $preference['title']]
            );
        };

        if ($all !== true) {
            return $map($this->get(), $this->lastNamespace);
        }

        return collect($this->getAll())
            ->map(fn ($preferences, $namespace) => $map($preferences, $namespace))
            ->collapse()
            ->sortByDesc(fn ($_, $key) => str_starts_with($key, 'core.'));
    }

    public function toDottedAll(): Collection
    {
        return $this->toDotted(true);
    }

    public function toPermissions(): Collection
    {
        return $this->toDotted()->keys()->map(fn ($bag) => "preference:{$bag}");
    }
}
