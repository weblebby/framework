<?php

namespace Feadmin\Hooks;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Menu
{
    private Panel $panel;

    private string $lastLocation;

    private string $lastCategory;

    private array $menus = [];

    public function __construct(Panel $panel)
    {
        $this->panel = $panel;
    }

    public function location(string $location): self
    {
        $this->lastLocation = $location;
        $this->menus[$this->lastLocation] ??= [];

        return $this;
    }

    public function category(string $category, string $title = null): self
    {
        $this->lastCategory = $category;

        $this->menus[$this->lastLocation][$this->lastCategory] ??= [
            'title' => $title,
        ];

        return $this;
    }

    public function add(array $item): self
    {
        $items = $this->menus[$this->lastLocation][$this->lastCategory]['items'] ?? [];

        $this->menus[$this->lastLocation][$this->lastCategory]['items'][] = [
            'position' => count($items) * 10,
            ...$item,
        ];

        return $this;
    }

    public function get(): Collection
    {
        return collect($this->menus[$this->lastLocation])
            ->map(function ($location) {
                $location['items'] = collect($location['items'] ?? [])
                    ->filter(fn ($item) => $this->userCanDisplay($item))
                    ->sortBy('position')
                    ->values();

                return $location;
            })
            ->filter(function ($location) {
                return $location['items']->count() > 0;
            })
            ->sortBy('position');
    }

    private function userCanDisplay(array $item): bool
    {
        if (isset($item['can'])) {
            if (auth()->user()->hasRole('Super Admin')) {
                return true;
            }

            $item['can'] = Arr::wrap($item['can']);

            foreach ($item['can'] as $value) {
                if (is_string($value) && auth()->user()->cannot($value)) {
                    return false;
                }

                if (is_callable($value) && !$value()) {
                    return false;
                }
            }
        }

        return true;
    }
}
