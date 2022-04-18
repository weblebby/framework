<?php

namespace Feadmin\Items;

class MenuItem
{
    private string $title;

    private string $url;

    private bool $isActive = false;

    private ?string $icon = null;

    private ?string $badge = null;

    private string|array|null $can = null;

    private array $children = [];

    public static function create(string $title): static
    {
        return new static($title);
    }

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function icon($icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function badge($badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function isActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function can(string|array $can): self
    {
        $this->can = $can;

        return $this;
    }

    public function children(array $items): self
    {
        $this->children = $items;

        return $this;
    }

    public function get(): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'is_active' => $this->isActive,
            'icon' => $this->icon,
            'badge' => $this->badge,
            'can' => $this->can,
            'children' => array_map(function ($child) {
                return $child instanceof static ? $child->get() : $child;
            }, $this->children),
        ];
    }
}
