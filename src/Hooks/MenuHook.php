<?php

namespace Feadmin\Hooks;

use Feadmin\Items\MenuItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class MenuHook
{
    protected string $currentBag;

    protected string $currentCategory;

    protected array $menus = [];

    public function withBag(string $bag): self
    {
        $this->currentBag = $bag;
        $this->menus[$this->currentBag] ??= [];

        return $this;
    }

    public function withCategory(string $category, ?string $title = null): self
    {
        $this->currentCategory = $category;

        $this->menus[$this->currentBag][$this->currentCategory] ??= [
            'title' => $title,
        ];

        return $this;
    }

    public function add(MenuItem|array $item): self
    {
        if ($item instanceof MenuItem) {
            $item = $item->toArray();
        }

        $items = $this->menus[$this->currentBag][$this->currentCategory]['items'] ?? [];

        $this->menus[$this->currentBag][$this->currentCategory]['items'][] = [
            ...$item,
            'position' => $item['position'] ?? count($items) * 10,
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

    public function get(): Collection
    {
        return collect($this->menus[$this->currentBag])
            ->map(function ($bag) {
                $bag['items'] = collect($bag['items'] ?? [])
                    ->filter(fn ($item) => $this->canDisplay($item))
                    ->sortBy('position')
                    ->values();

                return $bag;
            })
            ->filter(fn ($bag) => $bag['items']->count() > 0)
            ->sortBy('position');
    }

    private function canDisplay(array $item): bool
    {
        if (isset($item['can'])) {
            $user = auth()->user();

            if ($user->hasRole('Super Admin')) {
                return true;
            }

            $item['can'] = Arr::wrap($item['can']);

            foreach ($item['can'] as $value) {
                if (is_string($value) && auth()->user()->cannot($value)) {
                    return false;
                }

                if (is_callable($value) && ! $value($user)) {
                    return false;
                }
            }
        }

        return true;
    }
}
