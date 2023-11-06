<?php

namespace Feadmin\Items;

use Exception;
use Feadmin\Enums\FieldTypeEnum;

class PreferenceItem
{
    private ?string $key;

    private FieldTypeEnum $type;

    private ?string $label = null;

    private ?string $hint = null;

    private ?string $default = null;

    private bool $translatable = false;

    private array $rules = [];

    private array $options = [];

    public static function paragraph(string $text): static
    {
        return (new static())
            ->type(FieldTypeEnum::PARAGRAPH)
            ->default($text);
    }

    public static function text(string $key): static
    {
        return (new static($key))->type(FieldTypeEnum::TEXT);
    }

    public static function tel(string $key): static
    {
        return (new static($key))->type(FieldTypeEnum::TEL);
    }

    public static function number(string $key): static
    {
        return (new static($key))->type(FieldTypeEnum::NUMBER);
    }

    public static function textarea(string $key): static
    {
        return (new static($key))->type(FieldTypeEnum::TEXT_AREA);
    }

    public static function image(string $key): static
    {
        return (new static($key))->type(FieldTypeEnum::IMAGE);
    }

    public static function richText(string $key): static
    {
        return (new static($key))->type(FieldTypeEnum::RICH_TEXT);
    }

    public static function select(string $key): static
    {
        return (new static($key))->type(FieldTypeEnum::SELECT);
    }

    public static function checkbox(string $key): static
    {
        return (new static($key))->type(FieldTypeEnum::CHECKBOX);
    }

    public static function radio(string $key): static
    {
        return (new static($key))->type(FieldTypeEnum::RADIO);
    }

    public function __construct(?string $key = null)
    {
        $this->key = $key;
    }

    public function type(FieldTypeEnum $type): self
    {
        $this->type = $type;

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

    public function default(?string $default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function translatable(bool $translatable = true): self
    {
        if (!$this->type->isTranslatable()) {
            throw new Exception(sprintf('Field type [%s] is not translatable.', $this->type->name));
        }

        $this->translatable = $translatable;

        return $this;
    }

    public function rules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'type' => $this->type,
            'label' => $this->label,
            'hint' => $this->hint,
            'default' => $this->default,
            'translatable' => $this->translatable,
            'rules' => $this->rules,
            'options' => $this->options,
        ];
    }
}
