<?php

namespace Weblebby\Framework\Items\Field;

use Illuminate\View\ComponentAttributeBag;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Items\Field\Concerns\HasFieldName;

class TextFieldItem extends FieldItem
{
    use HasFieldName;

    protected ?array $attributes = null;

    protected ?string $label = null;

    protected ?string $hint = null;

    protected mixed $default = null;

    protected bool $translatable = false;

    protected array $rules = [];

    public function __construct(?string $key = null)
    {
        parent::__construct($key);

        $this->name($key);
    }

    public function attributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
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

    public function default(mixed $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function translatable(bool $translatable = true): self
    {
        if (! Extension::has('multilingual')) {
            return $this;
        }

        $this->translatable = $translatable;

        return $this;
    }

    public function rules(array $rules, bool $merge = true): self
    {
        if ($merge) {
            $this->rules = array_merge($this->rules, $rules);
        } else {
            $this->rules = $rules;
        }

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'name' => $this->name,
            'indexed_name' => $this->indexedName,
            'attributes' => new ComponentAttributeBag($this->attributes ?? []),
            'label' => $this->label,
            'hint' => $this->hint,
            'default' => $this->default,
            'translatable' => $this->translatable,
            'rules' => $this->rules,
        ]);
    }
}
