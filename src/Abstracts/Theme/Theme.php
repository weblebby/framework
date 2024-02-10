<?php

namespace Weblebby\Framework\Abstracts\Theme;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonSerializable;
use Weblebby\Framework\Concerns\HasArray;
use Weblebby\Framework\Concerns\HasViewAndRoutes;
use Weblebby\Framework\Items\FieldSectionsItem;

abstract class Theme implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray, HasViewAndRoutes;

    protected array $variants;

    abstract public function name(): string;

    abstract public function title(): string;

    abstract public function description(): string;

    /**
     * @return array<int, class-string>
     */
    abstract public function templates(): array;

    /**
     * @return array<int, class-string>
     */
    abstract public function variants(): array;

    abstract public function preferences(): FieldSectionsItem;

    /**
     * @return array<int, string>
     */
    public function installmentPreferenceKeys(): array
    {
        return [];
    }

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

    /**
     * @return Collection<int, Variant>
     */
    public function getVariants(): Collection
    {
        return collect($this->variants())
            ->map(fn (string $variant) => new $variant($this))
            ->values();
    }

    public function namespace(): string
    {
        return sprintf('theme-%s', $this->name());
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
