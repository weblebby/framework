<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Exception;
use Feadmin\Concerns\Fieldable;
use Feadmin\Concerns\HasArray;
use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Support\FormComponent;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Validation\Rule;
use JsonSerializable;

class FieldItem implements Arrayable, ArrayAccess, Fieldable, Jsonable, JsonSerializable
{
    use HasArray;

    private ?string $key;

    private ?string $name;

    private FieldTypeEnum $type;

    private ?string $label = null;

    private ?string $hint = null;

    private ?string $default = null;

    private bool $translatable = false;

    private array $rules = [];

    private array $options = [];

    private ?float $position = 0;

    public static function repeated(string $key): RepeatedFieldItem
    {
        return new RepeatedFieldItem($key);
    }

    public static function grouped(string $key): GroupedFieldItem
    {
        return new GroupedFieldItem($key);
    }

    public static function conditional(string $key, string $value, array $fields): ConditionalFieldItem
    {
        $conditions = [
            'key' => $key,
            'value' => $value,
            'operator' => '===',
        ];

        return (new ConditionalFieldItem())
            ->conditions([$conditions])
            ->fields($fields);
    }

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

    public function __construct(string $key = null)
    {
        $this->key = FormComponent::nameToDotted($key);

        $this->name($key);
    }

    public function name(?string $name): self
    {
        $this->name = $name;

        return $this;
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
            throw new Exception(sprintf('Fieldable type [%s] is not translatable.', $this->type->name));
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

    public function options(array $options): self
    {
        $this->options = $options;

        if (count($this->options) > 0) {
            $this->rules[] = Rule::in(array_keys($this->options));
        }

        return $this;
    }

    public function position(float $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'type' => $this->type,
            'label' => $this->label,
            'hint' => $this->hint,
            'default' => $this->default,
            'translatable' => $this->translatable,
            'rules' => $this->rules,
            'options' => $this->options,
            'position' => $this->position,
        ];
    }
}
