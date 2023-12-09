<?php

namespace Feadmin\Items\Field;

use Illuminate\Validation\Rule;

class SelectFieldItem extends TextFieldItem
{
    protected array $options = [];

    public function options(array $options): self
    {
        $this->options = $options;

        if (count($this->options) > 0) {
            $this->rules[] = Rule::in(array_keys($this->options));
        }

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
        ]);
    }
}