<?php

namespace Weblebby\Framework\Abstracts\Theme;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Weblebby\Framework\Concerns\HasArray;

abstract class Variant implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    abstract public function name(): string;

    abstract public function title(): string;

    abstract public function handle(): void;

    public function __construct(protected Theme $theme)
    {
        //
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'title' => $this->title(),
        ];
    }
}
