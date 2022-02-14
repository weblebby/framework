<?php

namespace Feadmin\Services;

use Illuminate\Support\Arr;

class FormComponentService
{
    public function name(?string $name): string
    {
        return $name;
    }

    public function dottedName(?string $name): string
    {
        return str_replace('[]', '', $name);
    }

    public function id(?string $id): string
    {
        return str_replace('[]', '', $id);
    }

    public function checked(string $name, mixed $default, $attributes): bool
    {
        return session()->hasOldInput()
            ? in_array($attributes->get('value'), Arr::wrap(old($name)))
            : (bool) $default;
    }
}
