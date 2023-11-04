<?php

namespace Feadmin\Items;

class MenuItem
{
    protected string $title;

    protected string $url;

    protected bool $isActive = false;

    protected ?string $icon = null;

    protected ?string $badge = null;

    protected string|array|null $can = null;

    protected array $children = [];

    public static function create(string $title): static
    {
        return new static($title);
    }

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function withIcon($icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function withBadge($badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function withUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function withAbility(string|array $can): self
    {
        $this->can = $can;

        return $this;
    }

    public function withChildren(array $items): self
    {
        $this->children = $items;

        return $this;
    }

    public function withActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function activate(): self
    {
        $this->isActive = true;

        return $this;
    }

    public function deactivate(): self
    {
        $this->isActive = false;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'is_active' => $this->isActive,
            'icon' => $this->icon,
            'badge' => $this->badge,
            'can' => $this->can,
            'children' => array_map(
                fn($child) => $child instanceof static ? $child->toArray() : $child,
                $this->children,
            ),
        ];
    }
}
