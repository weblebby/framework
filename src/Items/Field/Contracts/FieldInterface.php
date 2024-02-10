<?php

namespace Weblebby\Framework\Items\Field\Contracts;

use Weblebby\Framework\Enums\FieldTypeEnum;

interface FieldInterface
{
    public function parent(?FieldInterface $parent): self;

    public function key(?string $key = null): self;

    public function type(FieldTypeEnum $type): self;

    public function position(float $position): self;

    public function toArray(): array;
}
