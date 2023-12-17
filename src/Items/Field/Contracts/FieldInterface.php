<?php

namespace Feadmin\Items\Field\Contracts;

use Feadmin\Enums\FieldTypeEnum;

interface FieldInterface
{
    public function parent(?FieldInterface $parent): self;

    public function type(FieldTypeEnum $type): self;

    public function position(float $position): self;

    public function toArray(): array;
}
