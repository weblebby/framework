<?php

namespace Feadmin\Items;

use Feadmin\Contracts\Eloquent\PostInterface;
use Feadmin\Facades\Theme;
use Feadmin\Items\Field\Contracts\FieldInterface;
use Illuminate\Support\Collection;

class FieldSectionsItem
{
    protected array $sections = [];

    public static function make(): self
    {
        return new static();
    }

    public function add(string $name, string $title, array $fields): self
    {
        $this->sections[$name] = [
            'title' => $title,
            'fields' => $fields,
        ];

        return $this;
    }

    public function withTemplateSections(PostInterface $postable, string $template = null): self
    {
        if (is_null($template)) {
            return $this;
        }

        $templates = Theme::active()->templatesFor($postable::class);
        $templateSections = $templates->firstWhere('name', $template)?->sections()?->toArray();

        if ($templateSections) {
            $this->sections = array_merge($this->sections, $templateSections);
        }

        return $this;
    }

    public function allFields(): Collection
    {
        return collect($this->sections)
            ->map(fn($section) => $section['fields'])
            ->flatten();
    }

    /**
     * @return array<string, array<string, string|array<int, FieldInterface>>>
     */
    public function toArray(): array
    {
        return $this->sections;
    }
}
