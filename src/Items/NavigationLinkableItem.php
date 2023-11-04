<?php

namespace Feadmin\Items;

use Illuminate\Support\Collection;

class NavigationLinkableItem
{
    protected string $name;

    protected string $title;

    protected ?float $position = null;

    protected string $model;

    protected Collection $links;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setPosition(float $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function setLinks(Collection $links): self
    {
        $this->links = $links;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function position(): ?float
    {
        return $this->position;
    }

    public function model(): string
    {
        return $this->model;
    }

    public function links(): Collection
    {
        return $this->links;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
            'position' => $this->position,
            'model' => $this->model,
            'links' => $this->links->toArray(),
        ];
    }
}
