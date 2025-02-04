<?php

namespace Weblebby\Framework\Hooks;

use Illuminate\Support\Collection;
use Weblebby\Framework\Facades\Preference;
use Weblebby\Framework\Items\Field\Collections\FieldCollection;
use Weblebby\Framework\Items\Field\Contracts\FieldInterface;

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

    public function withBag(string $bag, string $title, ?float $position = null): self
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
        $bag = $this->get()[$bag] ?? null;

        if (is_null($bag)) {
            return null;
        }

        $field = head(array_filter($bag['fields'], fn ($field) => $field['key'] === $key));

        if ($field === false) {
            return null;
        }

        return $field;
    }

    public function fields(string $bag, ?string $locale = null): FieldCollection
    {
        $fields = $this->get($locale)[$bag]['fields'] ?? [];

        return (new FieldCollection($fields))
            ->sortBy('position')
            ->values();
    }

    public function getAll(): array
    {
        if (! $this->authorization) {
            return $this->namespaces;
        }

        return collect($this->namespaces)
            ->map(function ($preferences) {
                return array_filter(
                    $preferences,
                    fn ($item) => auth()->check() && auth()->user()->can($item['permission'])
                );
            })
            ->toArray();
    }

    public function get(?string $locale = null): array
    {
        return collect($this->getAll()[$this->currentNamespace])
            ->sortBy('position')
            ->map(function ($preference, $bag) use ($locale) {
                $bag = Preference::namespaces($this->currentNamespace)[$bag]['fields'] ?? null;

                if (is_null($bag)) {
                    return null;
                }

                $bag = array_map(function (FieldInterface $field) use ($locale) {
                    if (isset($field['name']) && method_exists($field, 'default')) {
                        $field->default(
                            preference(
                                rawKey: $field['name'],
                                default: $field['default'] ?? null,
                                locale: $locale,
                            )
                        );
                    }

                    return $field;
                }, $bag);

                return [...$preference, 'fields' => $bag];
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

        if ($all === false) {
            return $map($this->get(), $this->currentNamespace);
        }

        return collect($this->getAll())
            ->map(fn ($preferences, $namespace) => $map($preferences, $namespace))
            ->collapse()
            ->sortByDesc(fn ($_, $key) => str_starts_with($key, 'default.'));
    }

    public function toPermissions(): Collection
    {
        return $this->toDotted()->keys()->map(fn ($bag) => "preference:{$bag}");
    }
}
