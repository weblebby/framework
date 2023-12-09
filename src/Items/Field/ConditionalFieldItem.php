<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Feadmin\Concerns\Fieldable;
use Feadmin\Concerns\HasArray;
use Feadmin\Enums\FieldTypeEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class ConditionalFieldItem extends FieldItem
{
    use HasChildFields, HasFieldName;

    protected array $conditions = [];

    public function __construct(string $key = null)
    {
        parent::__construct($key);

        $this->type = FieldTypeEnum::CONDITIONAL;
    }

    public function conditions(array $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'name' => $this->name,
            'conditions' => $this->conditions,
            'fields' => $this->fields,
        ]);
    }
}
