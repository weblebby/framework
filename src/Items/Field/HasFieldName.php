<?php

namespace Feadmin\Items\Field;

use Illuminate\Support\Str;

trait HasFieldName
{
    protected ?string $name = null;

    public function name(?string $name): self
    {
        $prevName = $this->name;
        $this->name = $name;

        if (method_exists($this, 'fields')) {
            $this->setChildFieldsName($prevName);
        }

        return $this;
    }

    protected function setChildFieldsName(?string $prevName): void
    {
        if (is_null($prevName)) {
            return;
        }

        foreach ($this->fields as $field) {
            $field->name(Str::replaceFirst($prevName, $this->name, $field['name']));
        }
    }
}