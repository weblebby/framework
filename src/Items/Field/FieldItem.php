<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Feadmin\Concerns\HasArray;
use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Items\Field\Contracts\FieldInterface;
use Feadmin\Support\FormComponent;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class FieldItem implements FieldInterface, Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    protected ?FieldInterface $parent = null;

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

    public static function paragraph(string $text): InformationalFieldItem
    {
        return (new InformationalFieldItem())
            ->type(FieldTypeEnum::PARAGRAPH)
            ->body($text);
    }

    public static function text(string $key): TextFieldItem
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::TEXT);
    }

    public static function tel(string $key): TextFieldItem
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::TEL);
    }

    public static function number(string $key): TextFieldItem
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::NUMBER);
    }

    public static function textarea(string $key): TextFieldItem
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::TEXT_AREA);
    }

    public static function richText(string $key): TextFieldItem
    {
        return (new TextFieldItem($key))->type(FieldTypeEnum::RICH_TEXT);
    }

    public static function image(string $key): ImageFieldItem
    {
        return (new ImageFieldItem($key))->type(FieldTypeEnum::IMAGE);
    }

    public static function select(string $key): SelectFieldItem
    {
        return (new SelectFieldItem($key))->type(FieldTypeEnum::SELECT);
    }

    public static function checkbox(string $key): CheckboxFieldItem
    {
        return (new CheckboxFieldItem($key))->type(FieldTypeEnum::CHECKBOX);
    }

    public static function radio(string $key): RadioFieldItem
    {
        return (new RadioFieldItem($key))->type(FieldTypeEnum::RADIO);
    }

    public function __construct(string $key = null)
    {
        $this->key = FormComponent::nameToDotted($key);
    }

    public function parent(?FieldInterface $parent): self
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