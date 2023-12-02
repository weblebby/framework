<?php

namespace Feadmin\Items;

use Feadmin\Concerns\Fieldable;

class PostSectionsItem
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

    /**
     * @return array<string, array<string, string|array<int, Fieldable>>>
     */
    public function toArray(): array
    {
        return $this->sections;
    }
}
