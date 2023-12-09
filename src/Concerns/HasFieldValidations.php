<?php

namespace Feadmin\Concerns;

use Feadmin\Enums\FieldTypeEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasFieldValidations
{
    public function fieldsForValidation(Collection|array $fields, array $input): array
    {
        $rules = [];
        $attributes = [];

        foreach ($fields as $field) {
            $this->validateField($field, $input, $rules, $attributes);
        }

        return compact('rules', 'attributes');
    }

    protected function validateField(Fieldable $field, mixed $input, array &$rules, array &$attributes): void
    {
        if ($field['type'] === FieldTypeEnum::REPEATED) {
            $this->processRepeatedFieldForValidation($field, $input, $rules, $attributes);

            return;
        }

        if ($field['type'] === FieldTypeEnum::CONDITIONAL) {
            dd($field, $input);
            foreach ($field['fields'] as $child) {
                $this->validateField($child, Arr::get($input, $child['name']), $rules, $attributes);
            }

            return;
        }

        $attributes[$field['name']] = $field['label'];

        if ($field['type']?->isEditable() && isset($field['rules'])) {
            $rules[$field['name']] = $field['rules'];
        }
    }

    protected function processRepeatedFieldForValidation(Fieldable $field, mixed $input, array &$rules, array &$attributes): void
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
            $this->validateField($childField, $input, $rules, $attributes);
        }
    }
}