<?php

namespace Feadmin\Abstracts\Theme;

use ArrayAccess;
use Feadmin\Concerns\HasArray;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonSerializable;

abstract class Theme implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    abstract public function name(): string;

    abstract public function title(): string;

    abstract public function description(): string;

    /**
     * @return array<int, class-string>
     */
    abstract public function templates(): array;

    /**
     * @param  class-string  $postType
     * @return Collection<int, Template>
     */
    public function templatesFor(string $postType): Collection
    {
        return collect($this->templates())
            ->map(fn (string $template) => new $template())
            ->filter(fn (Template $template) => in_array($postType, Arr::wrap($template->postTypes())))
            ->values();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'title' => $this->title(),
            'description' => $this->description(),
            'templates' => $this->templates(),
        ];
    }
}
