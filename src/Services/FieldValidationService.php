<?php

namespace Feadmin\Services;

use Feadmin\Concerns\Fieldable;
use Feadmin\Items\Field\ConditionalFieldItem;
use Feadmin\Items\Field\RepeatedFieldItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FieldValidationService
{
    public function get(Collection|array $fields, Collection $fieldsWithInput): array
    {
        $rules = [];
        $attributes = [];

        foreach ($fields as $field) {
            $this->validate($field, Arr::get($fieldsWithInput, $field['name']), $rules, $attributes);
        }

        return compact('rules', 'attributes');
    }

    protected function validate(Fieldable $field, mixed $input, array &$rules, array &$attributes): void
    {
        if ($field instanceof RepeatedFieldItem) {
            $this->validateRepeatedField($field, $input, $rules, $attributes);

            return;
        }

        if ($field instanceof ConditionalFieldItem) {
            $this->validateConditionalField($field, $input, $rules, $attributes);

            return;
        }

        $attributes[$field['name']] = $field['label'];

        if ($field['type']?->isEditable() && isset($field['rules'])) {
            $rules[$field['name']] = $field['rules'];
        }
    }

    protected function validateRepeatedField(Fieldable $field, mixed $input, array &$rules, array &$attributes): void
    {
        $rules[$field['name']] = ['nullable', 'array'];

        if ($field['max']) {
            $rules[$field['name']][] = "max:{$field['max']}";
        }

        foreach ($field['field_rules'] as $key => $rule) {
            $rules[$key] = $rule;
        }

        foreach ($field['field_labels'] as $key => $label) {
            $attributes[$key] = $label;
        }

        foreach ($field['fields'] as $childField) {
            $this->validate($childField, $input, $rules, $attributes);
        }
    }

    protected function validateConditionalField(Fieldable $field, mixed $input, array &$rules, array &$attributes): void
    {
        foreach ($input ?? [] as $key => $value) {
            $allConditionsPassed = true;

            foreach ($field['conditions'] as $condition) {
                $conditionIdentifier = sprintf('%s.value', $condition['key']);
                $inputValue = Arr::get($value, $conditionIdentifier);

                if (!$this->validateCondition($condition['operator'], $inputValue, $condition['value'])) {
                    $allConditionsPassed = false;
                    break;
                }
            }

            if (!$allConditionsPassed) {
                continue;
            }

            foreach ($field['fields'] as $childField) {
                // FIXME: Dont change original object name
                $childField->name(sprintf('%s.%d.%s', $field['name'], $key, $childField['key']));
                $this->validate($childField, $value[$childField['key']] ?? [], $rules, $attributes);
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
            'not_in' => !in_array($value, $conditionValue),
            'between' => $value >= $conditionValue[0] && $value <= $conditionValue[1],
            'not_between' => $value < $conditionValue[0] || $value > $conditionValue[1],
            'contains' => Str::contains($value, $conditionValue),
            'not_contains' => !Str::contains($value, $conditionValue),
            'starts_with' => Str::startsWith($value, $conditionValue),
            'ends_with' => Str::endsWith($value, $conditionValue),
            'regex' => preg_match($conditionValue, $value),
            'not_regex' => !preg_match($conditionValue, $value),
            default => false,
        };
    }
}