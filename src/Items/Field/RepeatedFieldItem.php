<?php

namespace Feadmin\Items\Field;

use ArrayAccess;
use Feadmin\Concerns\Fieldable;
use Feadmin\Concerns\HasArray;
use Feadmin\Enums\FieldTypeEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class RepeatedFieldItem implements Arrayable, ArrayAccess, Fieldable, Jsonable, JsonSerializable
{
    use HasArray;
    
    private string $key;

    private string $name;

    private ?string $label = null;

    private ?string $hint = null;

    private ?array $default = [];

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

    public function default(array $fields): self
    {
        $this->default = $fields;

        return $this;
    }

    public function fields(array $fields): self
    {
        $this->fields = collect($fields)
            ->map(function (Fieldable $field) {
                $this->setFieldName($field);

                return $field;
            })
            ->all();

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
            if ($field['type']->isInformational()) {
                continue;
            }

            // $key = sprintf('%s.*.%s', $this->name, $field['name']);
            $labels[$field['name']] = $field['label'];
        }

        return $labels;
    }

    public function fieldRules(): array
    {
        $rules = [];

        foreach ($this->fields as $field) {
            if ($field['type']->isInformational()) {
                continue;
            }

            // $key = sprintf('%s.*.%s', $this->name, $field['name']);
            $rules[$field['name']] = $field['rules'];
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
            'default' => $this->default,
            'fields' => $this->fields,
            'max' => $this->max,
            'position' => $this->position,
            'field_rules' => $this->fieldRules(),
            'field_labels' => $this->fieldLabels(),
        ];
    }

    protected function setFieldName(Fieldable $field, string $parentKey = null): void
    {
        if (method_exists($field, 'name')) {
            $name = is_null($parentKey)
                ? sprintf('%s.*.%s', $this->name, $field['key'])
                : "{$parentKey}.*.{$field['key']}";

            $field->name($name);
        }

        if (method_exists($field, 'fields')) {
            collect($field['fields'])->each(function (Fieldable $child) use ($field) {
                $this->setFieldName($child, $field['name'] ?? null);
            });
        }
    }
}
