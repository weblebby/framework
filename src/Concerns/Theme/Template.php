<?php

namespace Feadmin\Concerns\Theme;

use ArrayAccess;
use Feadmin\Concerns\HasArray;
use Feadmin\Items\Field\FieldItem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

abstract class Template implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    /**
     * @return class-string|array<int, class-string>
     */
    abstract public function postTypes(): string|array;

    abstract public function name(): string;

    abstract public function title(): string;

    /**
     * @return array<string, string>
     */
    abstract public function tabs(): array;

    /**
     * @return array<string, array<int, FieldItem>>
     */
    abstract public function fields(): array;

    public function toArray(): array
    {
        return [
            'post_types' => $this->postTypes(),
            'name' => $this->name(),
            'title' => $this->title(),
            'tabs' => $this->tabs(),
            'fields' => $this->fields(),
        ];
    }
}
