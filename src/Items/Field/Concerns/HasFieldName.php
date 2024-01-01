<?php

namespace Feadmin\Items\Field\Concerns;

use Illuminate\Support\Str;

trait HasFieldName
{
    protected ?string $name = null;

    protected ?string $indexedName = null;

    public function name(?string $name): self
    {
        if (is_null($this->parent) && !Str::startsWith($name, 'fields.')) {
            $name = "fields.{$name}";
        }

        $prevName = $this->name;
        $this->name = $name;

        if (method_exists($this, 'fields')) {
            $this->setChildFieldsName($prevName);
        }

        return $this;
    }

    public function indexedName(?string $indexedName): self
    {
        $prevName = $this->indexedName ?? $this->name;
        $this->indexedName = $indexedName;

        if (method_exists($this, 'fields')) {
            $this->setChildFieldsName($prevName, 'indexedName');
        }

        return $this;
    }

    protected function setChildFieldsName(?string $prevName, string $method = 'name'): void
    {
        if (is_null($prevName)) {
            return;
        }

        foreach ($this->fields as $field) {
            $field->{$method}(Str::replaceFirst($prevName, $this->{$method}, $field['name']));
        }
    }
}