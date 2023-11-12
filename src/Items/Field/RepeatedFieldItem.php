<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Feadmin\Concerns\Fieldable;
use Feadmin\Enums\FieldTypeEnum;
use JsonSerializable;
use Feadmin\Concerns\HasArray;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class RepeatedFieldItem implements Fieldable, Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    private string $key;

    private string $name;

    private ?string $label = null;

    private ?string $hint = null;

    private array $fields;

    private ?int $max = null;

    private float $position = 0;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->name = $key;
    }

    public function name(string $name): self
    {
        $this->name = $name;

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

    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function max(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function position(float $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function fieldLabels(): array
    {
        $labels = [];

        foreach ($this->fields as $field) {
            $key = sprintf('%s.*.%s', $this->name, $field['name']);
            $labels[$key] = $field['label'];
        }

        return $labels;
    }

    public function fieldRules(): array
    {
        $rules = [];

        foreach ($this->fields as $field) {
            $key = sprintf('%s.*.%s', $this->name, $field['name']);
            $rules[$key] = $field['rules'];
        }

        return $rules;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'type' => FieldTypeEnum::REPEATED,
            'label' => $this->label,
            'hint' => $this->hint,
            'fields' => $this->fields,
            'max' => $this->max,
            'position' => $this->position,
            'field_rules' => $this->fieldRules(),
            'field_labels' => $this->fieldLabels(),
        ];
    }
}
