<?php

namespace Feadmin\Hooks;

use Feadmin\Facades\Preference;
use Illuminate\Support\Collection;

class PreferenceBagHook
{
    protected string $currentNamespace;

    protected array $namespaces = [];

    private bool $authorization = true;

    public function withAuthorization(): self
    {
        $this->authorization = true;

        return $this;
    }

    public function ignoreAuthorization(): self
    {
        $this->authorization = false;

        return $this;
    }

    public function withNamespace(string $namespace): self
    {
        $this->currentNamespace = $namespace;
        $this->namespaces[$this->currentNamespace] ??= [];

        return $this;
    }

    public function withBag(string $bag, string $title, float $position = null): self
    {
        $this->namespaces[$this->currentNamespace][$bag] = [
            'title' => $title,
            'position' => is_null($position)
                ? (count($this->namespaces[$this->currentNamespace] ?? []) * 10)
                : $position,
            'permission' => "preference:{$this->currentNamespace}.{$bag}",
        ];

        return $this;
    }

    public function field(string $bag, string $key): ?array
    {
        $preferences = $this->get();
        $bag = $preferences[$bag] ?? null;

        if (is_null($bag)) {
            return null;
        }

        return head(array_filter($bag['fields'], fn($field) => $field['key'] === $key));
    }

    public function fields(string $bag): Collection
    {
        return collect($this->get()[$bag]['fields'] ?? [])
            ->sortBy('position')
            ->values();
    }

    public function getAll(): array
    {
        if (!$this->authorization) {
            return $this->namespaces;
        }

        return collect($this->namespaces)
            ->map(function ($preferences) {
                return array_filter(
                    $preferences,
                    fn($item) => auth()->check() && auth()->user()->can($item['permission'])
                );
            })
            ->toArray();
    }

    public function get(): array
    {
        return collect($this->getAll()[$this->currentNamespace])
            ->sortBy('position')
            ->map(function ($preference, $namespace) {
                $rest = Preference::namespaces($this->currentNamespace)[$namespace] ?? null;

                if (is_null($rest)) {
                    return null;
                }

                return [...$preference, ...$rest];
            })
            ->filter()
            ->toArray();
    }

    public function toDotted(bool $all = false): Collection
    {
        $map = function ($preferences, $namespace) {
            return collect($preferences)->mapWithKeys(
                fn($preference, $key) => ["{$namespace}.{$key}" => $preference['title']]
            );
        };

        if ($all === false) {
            return $map($this->get(), $this->currentNamespace);
        }

        return collect($this->getAll())
            ->map(fn($preferences, $namespace) => $map($preferences, $namespace))
            ->collapse()
            ->sortByDesc(fn($_, $key) => str_starts_with($key, 'default.'));
    }

    public function toPermissions(): Collection
    {
        return $this->toDotted()->keys()->map(fn($bag) => "preference:{$bag}");
    }
}
