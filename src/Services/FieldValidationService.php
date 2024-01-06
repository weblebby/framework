<?php

namespace Feadmin\Services;

use Feadmin\Items\Field\Concerns\HasFieldName;
use Feadmin\Items\Field\ConditionalFieldItem;
use Feadmin\Items\Field\Contracts\FieldInterface;
use Feadmin\Items\Field\Contracts\HasChildFieldInterface;
use Feadmin\Items\Field\FieldValueItem;
use Feadmin\Items\Field\RepeatedFieldItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FieldValidationService
{
    public function get(Collection|array $fields, array $fieldsWithInput): array
    {
        $rules = [];
        $attributes = [];

        foreach ($fields as $field) {
            if (! isset($field['name'])) {
                continue;
            }

            $fieldValue = Arr::get($fieldsWithInput, $field['name']);
            $this->validate($field, $fieldValue, $rules, $attributes);
        }

        return compact('rules', 'attributes');
    }

    protected function validate(FieldInterface $field, ?FieldValueItem $fieldValueItem, array &$rules, array &$attributes): void
    {
        if (! isset($field['name'])) {
            return;
        }

        if ($field instanceof RepeatedFieldItem) {
            $this->validateRepeatedField($field, $fieldValueItem, $rules, $attributes);

            return;
        }

        if ($field instanceof ConditionalFieldItem) {
            $this->validateConditionalField($field, $fieldValueItem, $rules, $attributes);

            return;
        }

        if ($fieldValueItem?->field() instanceof RepeatedFieldItem) {
            return;
        }

        $name = $fieldValueItem?->field()['indexed_name'] ?? $field['name'];
        $attributes[$name] = $field['label'];

        if ($field['type']?->isEditable() && isset($field['rules'])) {
            $rules[$name] = $field['rules'];
        }
    }

    protected function validateRepeatedField(FieldInterface $field, ?FieldValueItem $fieldValueItem, array &$rules, array &$attributes): void
    {
        $name = $fieldValueItem?->field()['indexed_name'] ?? $field['name'];

        $rules[$name] = ['nullable', 'array'];

        if ($field['max']) {
            $rules[$name][] = "max:{$field['max']}";
        }

        foreach ($field['field_rules'] as $key => $rule) {
            $rules[$key] = $rule;
        }

        foreach ($field['field_labels'] as $key => $label) {
            $attributes[$key] = $label;
        }

        foreach ($field['fields'] as $childField) {
            $this->validate($childField, $fieldValueItem, $rules, $attributes);
        }
    }

    protected function validateConditionalField(
        FieldInterface $field,
        ?FieldValueItem $fieldValueItem,
        array &$rules,
        array &$attributes
    ): void {
        foreach ($fieldValueItem?->value() ?? [] as $value) {
            /*// FIXME: $value'yu loopa alınca preference tarafı çalışıyor fakat post tarafı çalışmıyor.
            // FIXME: Checkbox'a javascript ile true false değeri ver.
            foreach ($value as $childValue) {
                if ($childValue->field() instanceof HasChildFieldInterface) {
                    $this->validate($childValue->field(), $childValue, $rules, $attributes);
                    $validatedByChildField = true;
                }
            }

            if ($validatedByChildField ?? false) {
                continue;
            }*/

            $allConditionsPassed = true;

            foreach ($field['conditions'] as $condition) {
                $conditionIdentifier = sprintf('%s.value', $condition['key']);
                $inputValue = Arr::get($value, $conditionIdentifier);

                if (! $this->validateCondition($condition['operator'], $inputValue, $condition['value'])) {
                    $allConditionsPassed = false;
                    break;
                }
            }

            if (! $allConditionsPassed) {
                foreach ($value as $childValue) {
                    if ($childValue->field() instanceof HasChildFieldInterface) {
                        $this->validate($childValue->field(), $childValue, $rules, $attributes);
                    }
                }

                continue;
            }

            /** @var FieldInterface&HasFieldName $childField */
            foreach ($field['fields'] as $childField) {
                $childValue = Arr::get($value, $childField['key']);

                if (is_array($childValue)) {
                    $this->validate($childField, $fieldValueItem, $rules, $attributes);

                    continue;
                }

                $targetField = Arr::get($value, sprintf('%s.field', $childField['key']));

                if (is_null($targetField)) {
                    continue;
                }

                $this->validate($targetField, $value[$targetField['key']] ?? [], $rules, $attributes);
            }
        }
    }

    protected function validateCondition(string $operator, mixed $value, mixed $conditionValue): bool
    {
        return match ($operator) {
            '===' => $value === $conditionValue,
            '!==' => $value !== $conditionValue,
            '==' => $value == $conditionValue,
            '!=' => $value != $conditionValue,
            '>' => $value > $conditionValue,
            '>=' => $value >= $conditionValue,
            '<' => $value < $conditionValue,
            '<=' => $value <= $conditionValue,
            'in' => in_array($value, $conditionValue),
            'not_in' => ! in_array($value, $conditionValue),
            'between' => $value >= $conditionValue[0] && $value <= $conditionValue[1],
            'not_between' => $value < $conditionValue[0] || $value > $conditionValue[1],
            'contains' => Str::contains($value, $conditionValue),
            'not_contains' => ! Str::contains($value, $conditionValue),
            'starts_with' => Str::startsWith($value, $conditionValue),
            'ends_with' => Str::endsWith($value, $conditionValue),
            'regex' => preg_match($conditionValue, $value),
            'not_regex' => ! preg_match($conditionValue, $value),
            default => false,
        };
    }
}
