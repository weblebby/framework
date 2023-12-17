<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Feadmin\Concerns\HasArray;
use Feadmin\Items\Field\Contracts\FieldInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class FieldValueItem implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    public function __construct(protected FieldInterface $field, protected mixed $value)
    {
        //
    }

    public function field(): FieldInterface
    {
        return $this->field;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'value' => $this->value,
        ];
    }
}