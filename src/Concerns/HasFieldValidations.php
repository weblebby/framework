<?php

namespace Feadmin\Concerns;

use Feadmin\Enums\FieldTypeEnum;

trait HasFieldValidations
{
    public function fieldsForValidation(string $namespace, string $bag): array
    {
        $rules = [];
        $attributes = [];

        foreach ($this->fields($namespace, $bag) as $field) {
            $this->validateField($field, $rules, $attributes);
        }

        return compact('rules', 'attributes');
    }

    protected function validateField(Fieldable $field, array &$rules, array &$attributes): void
    {
        if ($field['type'] === FieldTypeEnum::REPEATED) {
            $this->processRepeatedFieldForValidation($field, $rules, $attributes);

            return;
        }

        $attributes[$field['name']] = $field['label'];

        if ($field['type']?->isEditable() && isset($field['rules'])) {
            $rules[$field['name']] = $field['rules'];
        }
    }

    protected function processRepeatedFieldForValidation(Fieldable $field, array &$rules, array &$attributes): void
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
            $this->validateField($childField, $rules, $attributes);
        }
    }
}