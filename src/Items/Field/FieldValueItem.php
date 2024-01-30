<?php

namespace Weblebby\Framework\Items\Field;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Weblebby\Framework\Concerns\HasArray;
use Weblebby\Framework\Items\Field\Contracts\FieldInterface;

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
