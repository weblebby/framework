<?php

namespace Feadmin\Services;

use Feadmin\Concerns\Fieldable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FieldInputService
{
    public function mapFieldsWithInput(Collection|array $fields, array $input): Collection
    {
        $computedFields = collect();

        foreach (Arr::dot($input) as $name => $value) {
            $field = $this->findFieldByName($name, $fields);

            if ($field) {
                $computedFields[$name] = [
                    'value' => $value,
                    'field' => clone $field,
                ];
            }
        }

        return $computedFields->undot();
    }

    protected function findFieldByName(string $name, Collection|array $fields): Fieldable|null
    {
        foreach ($fields as $field) {
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