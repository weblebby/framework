<?php

namespace Weblebby\Framework\Items;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Weblebby\Framework\Concerns\HasArray;

class SmartMenuItem implements \ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    use HasArray;

    protected string $name;

    protected string $title;

    protected ?float $position = null;

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

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
            'position' => $this->position,
        ];
    }
}
