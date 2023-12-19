<?php

namespace Feadmin\Items\Field\Collections;

use Feadmin\Items\Field\Contracts\FieldInterface;
use Feadmin\Items\Field\RepeatedFieldItem;
use Illuminate\Support\Collection;

class FieldCollection extends Collection
{
    public function findByName(string $name): ?FieldInterface
    {
        if (!str_starts_with($name, 'metafields.')) {
            $name = 'metafields.' . $name;
        }

        $name = preg_replace('/\.\d+\./', '.*.', $name);

        if ($first = $this->first(fn(FieldInterface $field) => $field['name'] === $name)) {
            return $first;
        }

        $field = $this->first(fn(FieldInterface $field) => str_starts_with($name, $field['name']));

        if ($field instanceof RepeatedFieldItem) {
            return (new static($field['fields']))->findByName($name);
        }

        return null;
    }
}