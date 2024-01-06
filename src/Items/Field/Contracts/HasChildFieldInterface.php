<?php

namespace Feadmin\Items\Field\Contracts;

interface HasChildFieldInterface
{
    public function fields(array $fields): self;

    public function fieldLabels(): array;

    public function fieldRules(): array;
}
