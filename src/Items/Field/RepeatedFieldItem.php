<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Feadmin\Concerns\Fieldable;
use Feadmin\Concerns\HasArray;
use Feadmin\Enums\FieldTypeEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class RepeatedFieldItem extends FieldItem
{
    use HasChildFields, HasFieldName;

    protected ?string $label = null;

    protected ?string $hint = null;

    protected ?array $default = [];

    protected ?int $max = null;

    public function __construct(string $key)
    {
        parent::__construct($key);

        $this->name($key);
        $this->type = FieldTypeEnum::REPEATED;
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

    public function default(array $fields): self
    {
        $this->default = $fields;

        return $this;
    }

    public function max(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'name' => $this->name,
            'label' => $this->label,
            'hint' => $this->hint,
            'default' => $this->default,
            'fields' => $this->fields,
            'max' => $this->max,
            'field_rules' => $this->fieldRules(),
            'field_labels' => $this->fieldLabels(),
        ]);
    }
}
