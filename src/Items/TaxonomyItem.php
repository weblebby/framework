<?php

namespace Feadmin\Items;

use ArrayAccess;
use Feadmin\Concerns\HasArray;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class TaxonomyItem implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    protected string $name;

    protected ?string $singularName = null;

    protected ?string $pluralName = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withSingularName(string $singularName): self
    {
        $this->singularName = $singularName;

        return $this;
    }

    public function withPluralName(string $pluralName): self
    {
        $this->pluralName = $pluralName;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function singularName(): string
    {
        return $this->singularName ?? str($this->name)->title()->toString();
    }

    public function pluralName(): string
    {
        return $this->pluralName ?? str($this->name)->title()->plural()->toString();
    }

    public function abilities(): array
    {
        return [
            'create' => sprintf('taxonomy:%s:create', $this->name),
            'read' => sprintf('taxonomy:%s:read', $this->name),
            'update' => sprintf('taxonomy:%s:update', $this->name),
            'delete' => sprintf('taxonomy:%s:delete', $this->name),
        ];
    }

    public function abilityFor(string $ability): ?string
    {
        return $this->abilities()[$ability] ?? null;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'singular_name' => $this->singularName(),
            'plural_name' => $this->pluralName(),
            'abilities' => $this->abilities(),
        ];
    }
}