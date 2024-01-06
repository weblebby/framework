<?php

namespace Feadmin\Items\Field;

use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Items\Field\Concerns\HasChildFields;
use Feadmin\Items\Field\Concerns\HasFieldName;
use Feadmin\Items\Field\Contracts\HasChildFieldInterface;

class ConditionalFieldItem extends FieldItem implements HasChildFieldInterface
{
    use HasChildFields, HasFieldName;

    protected array $conditions = [];

    public function __construct(?string $key = null)
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
            'indexed_name' => $this->indexedName,
            'conditions' => $this->conditions,
            'fields' => $this->fields,
            'field_rules' => $this->fieldRules(),
            'field_labels' => $this->fieldLabels(),
        ]);
    }
}
