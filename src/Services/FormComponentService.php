<?php

namespace Feadmin\Services;

use App\Support\Moneyable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use UnitEnum;

class FormComponentService
{
    public function name(?string $name): ?string
    {
        return $name;
    }

    public function dottedName(?string $name): ?string
    {
        if ($name) {
            return str_replace(['[]', ']', '['], ['', '', '.'], $name);
        }

        return null;
    }

    public function id(?string $id, string $bag = null): ?string
    {
        if ($id) {
            $id = str_replace('[]', '', $id);
        }

        if ($bag !== 'default' && !is_null($bag)) {
            $id = "{$bag}_{$id}";
        }

        return $id ?? null;
    }

    public function selected(?string $name, mixed $default, $attributes): bool
    {
        if ($default instanceof Model) {
            $default = $default->getKey();
        }

        if ($default instanceof UnitEnum) {
            $default = $default->value;
        }

        $value = filled($name) ? old($name, $default) : $default;

        return filled($value) && in_array(
            (string) $attributes->get('value'),
            Arr::wrap($value)
        );
    }

    public function value(mixed $value): ?string
    {
        if ($value instanceof Moneyable) {
            return $value->format();
        }

        return $value;
    }
}
