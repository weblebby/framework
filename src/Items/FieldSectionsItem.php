<?php

namespace Weblebby\Framework\Items;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Weblebby\Framework\Concerns\HasArray;
use Weblebby\Framework\Contracts\Eloquent\PostInterface;
use Weblebby\Framework\Facades\Theme;
use Weblebby\Framework\Items\Field\Collections\FieldCollection;
use Weblebby\Framework\Items\Field\Contracts\FieldInterface;

class FieldSectionsItem implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    protected array $sections = [];

    public static function make(): self
    {
        return new static();
    }

    public function add(
        string $name,
        string $title,
        array $fields,
        bool $prepend = false,
        ?int $position = null,
    ): self {
        if ($prepend === true) {
            foreach ($fields as $field) {
                if (method_exists($field, 'name')) {
                    $field->key("{$name}__{$field['key']}");
                    $field->name($field['key']);
                }
            }
        }

        if (isset($this->sections[$name])) {
            $this->sections[$name]['fields'] = array_merge(
                $this->sections[$name]['fields'],
                $fields,
            );

            return $this;
        }

        $this->sections[$name] = [
            'title' => $title,
            'fields' => $fields,
            'position' => $position ?? $this->getNewPosition(),
        ];

        return $this;
    }

    public function withTemplateSections(PostInterface $postable, ?string $template = null): self
    {
        if (is_null($template)) {
            return $this;
        }

        $templates = Theme::active()->templatesFor($postable::class);

        /** @var array<int, FieldSectionsItem> $templateSections */
        $templateSections = $templates->firstWhere('name', $template)?->sections()?->toArray();

        foreach ($templateSections ?? [] as $key => $section) {
            $this->add(
                $key,
                $section['title'],
                $section['fields'],
                position: $section['position'],
            );
        }

        return $this;
    }

    /**
     * @return FieldCollection<int, FieldInterface>
     */
    public function allFields(): FieldCollection
    {
        return (new FieldCollection($this->sections))
            ->map(fn ($section) => $section['fields'])
            ->flatten();
    }

    /**
     * @return array<string, array<string, string|array<int, FieldInterface>>>
     */
    public function toArray(): array
    {
        return collect($this->sections)->sortBy('position')->toArray();
    }

    private function getNewPosition(): int
    {
        return count($this->sections) * 10;
    }
}
