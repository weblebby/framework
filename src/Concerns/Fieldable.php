<?php

namespace Feadmin\Concerns;

use Feadmin\Enums\FieldTypeEnum;

interface Fieldable
{
    public function parent(?Fieldable $parent): self;

    public function type(FieldTypeEnum $type): self;

    public function position(float $position): self;

    public function toArray(): array;
}
