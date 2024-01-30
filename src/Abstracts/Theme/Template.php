<?php

namespace Weblebby\Framework\Abstracts\Theme;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Weblebby\Framework\Concerns\HasArray;
use Weblebby\Framework\Items\FieldSectionsItem;

abstract class Template implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    /**
     * @return class-string|array<int, class-string>
     */
    abstract public function postTypes(): string|array;

    abstract public function name(): string;

    abstract public function title(): string;

    abstract public function sections(): FieldSectionsItem;

    public function toArray(): array
    {
        return [
            'post_types' => $this->postTypes(),
            'name' => $this->name(),
            'title' => $this->title(),
            'sections' => $this->sections(),
        ];
    }
}
