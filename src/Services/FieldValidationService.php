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
        foreach ($field['conditions'] as $condition) {
            foreach ($input as $key => $value) {
                $conditionKey = sprintf("%d.%s", $key, $condition['key']);
                $fieldAndValue = Arr::get($input, $conditionKey);

                if ($fieldAndValue && $fieldAndValue['value'] === $condition['value']) {
                    foreach ($field['fields'] as $childField) {
                        dd($value);
                        $this->validate($childField, $input, $rules, $attributes);
                    }
                }
            }
        }
    }
}