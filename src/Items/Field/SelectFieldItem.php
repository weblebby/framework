<?php

namespace Weblebby\Framework\Items\Field;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\Rule;

class SelectFieldItem extends TextFieldItem
{
    protected array $options = [];

    public function options(array|Arrayable $options): self
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

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
