<?php

namespace Feadmin\Services;

use Feadmin\Items\Field\Concerns\HasFieldName;
use Feadmin\Items\Field\Contracts\FieldInterface;
use Feadmin\Items\Field\FieldValueItem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FieldInputService
{
    public function getFieldValues(Arrayable|array $fields, array $input): array
    {
        return Arr::undot($this->mapFieldsWithInput($fields, $input));
    }

    public function getDottedFieldValues(Arrayable|array $fieldValues): array
    {
        $dottedFields = [];

        foreach ($fieldValues as $fieldValue) {
            if ($fieldValue instanceof FieldValueItem && !is_array($fieldValue->value())) {
                $dottedFields[$fieldValue->field()['indexed_name']] = $fieldValue;
            }

            if (is_array($fieldValue)) {
                $dottedFields = array_merge($dottedFields, $this->getDottedFieldValues($fieldValue));
                continue;
            }

            if ($fieldValue instanceof FieldValueItem && is_array($fieldValue->value())) {
                $dottedFields = array_merge($dottedFields, $this->getDottedFieldValues($fieldValue->value()));
            }
        }

        return $dottedFields;
    }

    private function mapFieldsWithInput(Arrayable|array $fields, array $input, string $indexedNamePrefix = null): array
    {
        $computedFields = [];

        foreach ($input as $name => $value) {
            $prefixedName = $indexedNamePrefix ? $indexedNamePrefix . '.' . $name : $name;

            if (is_array($value)) {
                $computedFields += $this->processArrayValue($fields, $value, $prefixedName);
                continue;
            }

            if ($field = $this->findFieldByName($prefixedName, $fields)) {
                $computedFields[$prefixedName] = new FieldValueItem(
                    (clone $field)->indexedName($prefixedName),
                    $value
                );
            }

        }

        return $computedFields;
    }

    private function processArrayValue(Arrayable|array $fields, array $value, string $prefixedName): array
    {
        $computedFields = [];

        foreach ($value as $index => $item) {
            $childName = sprintf('%s.%s', $prefixedName, $index);
            $field = $this->findFieldByName($childName, $fields);

            if (is_null($field)) {
                continue;
            }

            if (is_array($item)) {
                $item = collect($this->mapFieldsWithInput($fields, $item, $childName))
                    ->mapWithKeys(function (FieldValueItem $item) use ($childName) {
                        $key = Str::replaceFirst($childName . '.', '', $item->field()['indexed_name']);

                        return [$key => $item];
                    })
                    ->undot()
                    ->all();
            }

            $computedFields[$childName] = new FieldValueItem((clone $field)->indexedName($childName), $item);
        }

        return $computedFields;
    }

    /**
     * @return FieldInterface&HasFieldName|null
     */
    private function findFieldByName(string $name, Arrayable|array $fields): FieldInterface|null
    {
        foreach ($fields as $field) {
            if (!isset($field['name'])) {
                continue;
            }

            if ($field['name'] === $name) {
                return $field;
            }

            if (filled($field['fields'] ?? null)) {
                $name = preg_replace('/\.\d+\./', '.*.', $name);
                $child = $this->findFieldByName($name, $field['fields']);

                if ($child) {
                    return $child;
                }
            }
        }

        return null;
    }
}