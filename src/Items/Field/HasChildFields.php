<?php

namespace Feadmin\Items\Field;

use Feadmin\Concerns\Fieldable;

trait HasChildFields
{
    protected array $fields = [];

    public function fields(array $fields): self
    {
        $this->fields = collect($fields)
            ->map(function (Fieldable $field) {
                $field->parent($this);
                $this->setFieldName($field);

                return $field;
            })
            ->all();

        return $this;
    }

    public function fieldLabels(): array
    {
        $labels = [];

        foreach ($this->fields as $field) {
            if ($field['type']->isInformational()) {
                continue;
            }

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

            $rules[$field['name']] = $field['rules'];
        }

        return $rules;
    }

    protected function setFieldName(Fieldable $field, string $parentKey = null): void
    {
        if (method_exists($field, 'name') && method_exists($this, 'name')) {
            $prefix = is_null($parentKey) ? $this->name : $parentKey;

            $name = $field['key']
                ? sprintf('%s.*.%s', $prefix, $field['key'])
                : $prefix;

            $field->name($name);
        }

        if (method_exists($field, 'fields')) {
            collect($field['fields'])->each(function (Fieldable $child) use ($field) {
                $this->setFieldName($child, $field['name'] ?? null);
            });
        }
    }
}