<?php

namespace Weblebby\Framework\Items;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;
use Weblebby\Framework\Concerns\HasArray;

class NavigationLinkableItem implements \ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    use HasArray;

    protected string $name;

    protected string $title;

    protected ?float $position = null;

    protected string $model;

    protected Collection $links;

    public static function make(): self
    {
        return new static();
    }

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
