<?php

namespace Feadmin\Items;

class PreferenceItem
{
    private string $key;

    private string $type;

    private string $label;

    private ?string $default = null;

    private array $rules = [];

    public static function text(string $key): static
    {
        return (new static($key))->type('text');
    }

    public static function textarea(string $key): static
    {
        return (new static($key))->type('textarea');
    }

    public static function image(string $key): static
    {
        return (new static($key))->type('image');
    }

    public static function richtext(string $key): static
    {
        return (new static($key))->type('richtext');
    }

    public static function select(string $key): static
    {
        return (new static($key))->type('select');
    }

    public static function checkbox(string $key): static
    {
        return (new static($key))->type('checkbox');
    }

    public static function radio(string $key): static
    {
        return (new static($key))->type('radio');
    }

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function default(string $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function rules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function get(): array
    {
        return [
            'key' => $this->key,
            'type' => $this->type,
            'label' => $this->label,
            'default' => $this->default,
            'rules' => $this->rules,
        ];
    }
}
