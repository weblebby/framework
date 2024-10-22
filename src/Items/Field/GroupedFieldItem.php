<?php

namespace Weblebby\Framework\Items\Field;

use Weblebby\Framework\Enums\FieldTypeEnum;
use Weblebby\Framework\Items\Field\Concerns\HasChildFields;
use Weblebby\Framework\Items\Field\Contracts\HasChildFieldInterface;

class GroupedFieldItem extends FieldItem implements HasChildFieldInterface
{
    use HasChildFields;

    protected ?string $label = null;

    protected ?string $hint = null;

    public function __construct(?string $key = null)
    {
        parent::__construct($key);

        $this->type = FieldTypeEnum::GROUPED;
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

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'label' => $this->label,
            'hint' => $this->hint,
            'fields' => $this->fields,
        ]);
    }
}
