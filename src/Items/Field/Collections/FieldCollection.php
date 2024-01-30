<?php

namespace Weblebby\Framework\Items\Field\Collections;

use Illuminate\Support\Collection;
use Weblebby\Framework\Items\Field\Contracts\FieldInterface;
use Weblebby\Framework\Items\Field\Contracts\HasChildFieldInterface;

class FieldCollection extends Collection
{
    public function findByName(string $name): ?FieldInterface
    {
        $name = preg_replace('/\.\d+\./', '.*.', $name);

        if ($first = $this->first(fn (FieldInterface $field) => $field['name'] === $name)) {
            return $first;
        }

        return $this
            ->filter(fn (FieldInterface $field) => $field instanceof HasChildFieldInterface)
            ->map(fn (HasChildFieldInterface $field) => (new static($field['fields']))->findByName($name))
            ->filter()
            ->first();
    }

    /**
     * @param  class-string  $instance
     */
    public function hasAnyTypeOf(string $instance): ?FieldInterface
    {
        if ($first = $this->first(fn (FieldInterface $field) => $field::class === $instance)) {
            return $first;
        }

        return $this
            ->filter(fn (FieldInterface $field) => $field instanceof HasChildFieldInterface)
            ->map(fn (HasChildFieldInterface $field) => (new static($field['fields']))->hasAnyTypeOf($instance))
            ->filter()
            ->first();
    }
}
