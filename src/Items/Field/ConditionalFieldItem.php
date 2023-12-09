<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Feadmin\Concerns\Fieldable;
use Feadmin\Concerns\HasArray;
use Feadmin\Enums\FieldTypeEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class ConditionalFieldItem implements Arrayable, ArrayAccess, Fieldable, Jsonable, JsonSerializable
{
    use HasArray;

    private array $conditions = [];

    private array $fields;

    private float $position = 0;

    public function conditions(array $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function fields(array $fields): self
    {
        $this->fields = array_map(function (Fieldable $field) {
            $field->parent($this);
            
            return $field;
        }, $fields);

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
            'type' => FieldTypeEnum::CONDITIONAL,
            'conditions' => $this->conditions,
            'fields' => $this->fields,
            'position' => $this->position,
        ];
    }
}
