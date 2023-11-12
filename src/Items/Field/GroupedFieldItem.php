<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Feadmin\Concerns\Fieldable;
use Feadmin\Enums\FieldTypeEnum;
use JsonSerializable;
use Feadmin\Concerns\HasArray;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class GroupedFieldItem implements Fieldable, Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    private string $key;

    private ?string $label = null;

    private ?string $hint = null;

    private array $fields;

    private float $position = 0;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function hint(string $hint): self
    {
        $this->hint = $hint;

        return $this;
    }

    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function position(float $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'type' => FieldTypeEnum::GROUPED,
            'label' => $this->label,
            'hint' => $this->hint,
            'fields' => $this->fields,
            'position' => $this->position,
        ];
    }
}
