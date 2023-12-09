<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Feadmin\Concerns\Fieldable;
use Feadmin\Concerns\HasArray;
use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Support\FormComponent;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class FieldItem implements Fieldable, Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    protected ?Fieldable $parent = null;

    protected ?string $key;

    protected FieldTypeEnum $type;

    protected ?float $position = 0;

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
        return (new InformationalFieldItem())
            ->type(FieldTypeEnum::PARAGRAPH)
            ->body($text);
    }

    public static function text(string $key): static
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::TEXT);
    }

    public static function tel(string $key): static
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::TEL);
    }

    public static function number(string $key): static
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::NUMBER);
    }

    public static function textarea(string $key): static
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::TEXT_AREA);
    }

    public static function richText(string $key): static
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::RICH_TEXT);
    }

    public static function image(string $key): static
    {
        return (new ImageFieldItem($key))->type(FieldTypeEnum::IMAGE);
    }

    public static function select(string $key): static
    {
        return (new SelectFieldItem($key))->type(FieldTypeEnum::SELECT);
    }

    public static function checkbox(string $key): static
    {
        return (new CheckboxFieldItem($key))->type(FieldTypeEnum::CHECKBOX);
    }

    public static function radio(string $key): static
    {
        return (new RadioFieldItem($key))->type(FieldTypeEnum::RADIO);
    }

    public function __construct(string $key = null)
    {
        $this->key = FormComponent::nameToDotted($key);
    }

    public function parent(?Fieldable $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function type(FieldTypeEnum $type): self
    {
        $this->type = $type;

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
            'parent' => $this->parent,
            'key' => $this->key,
            'type' => $this->type,
            'position' => $this->position,
        ];
    }
}
