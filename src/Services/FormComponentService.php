<?php

namespace Feadmin\Services;

use App\Support\Moneyable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use UnitEnum;

class FormComponentService
{
    public function name(?string $name): string
    {
        return $name;
    }

    public function dottedName(?string $name): string
    {
        return str_replace(['[]', ']', '['], ['', '', '.'], $name);
    }

    public function id(?string $id, string $bag = null): string
    {
        $id = str_replace('[]', '', $id);

        if ($bag !== 'default' && !is_null($bag)) {
            $id = "{$bag}_{$id}";
        }

        return $id;
    }

    public function checked(string $name, mixed $default, $attributes): bool
    {
        return session()->hasOldInput()
            ? in_array($attributes->get('value'), Arr::wrap(old($name)))
            : (bool) $default;
    }

    public function selected(string $name, mixed $default, $attributes): bool
    {
        if ($default instanceof Model) {
            $default = $default->getKey();
        }

        if ($default instanceof UnitEnum) {
            $default = $default->value;
        }

        return !is_null($old = old($name, $default))
            && (string) $old === (string) $attributes->get('value');
    }

    public function value(mixed $value): ?string
    {
        if ($value instanceof Moneyable) {
            return $value->format();
        }

        return $value;
    }
}
