<?php

namespace Feadmin\Hooks;

class MenuHook
{
    private string $lastLocation;

    private string $lastCategory;

    private array $menus = [];

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
        $this->menus[$this->lastLocation][$this->lastCategory]['items'][] = $item;

        return $this;
    }

    public function get(string $location = null): array
    {
        if (is_null($location)) {
            return $this->menus;
        }

        return $this->menus[$location];
    }
}
