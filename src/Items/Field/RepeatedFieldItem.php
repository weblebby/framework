<?php

namespace Weblebby\Framework\Items\Field;

use Weblebby\Framework\Enums\FieldTypeEnum;
use Weblebby\Framework\Items\Field\Concerns\HasChildFields;
use Weblebby\Framework\Items\Field\Concerns\HasFieldName;
use Weblebby\Framework\Items\Field\Contracts\HasChildFieldInterface;

class RepeatedFieldItem extends FieldItem implements HasChildFieldInterface
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
            'indexed_name' => $this->indexedName,
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
