<?php

namespace Feadmin\Items\Field;

trait HasFieldName
{
    protected ?string $name = null;

    public function name(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}