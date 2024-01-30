<?php

namespace Weblebby\Framework\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FormComponent
{
    public static function name(?string $name): ?string
    {
        return $name;
    }

    public static function dottedToName(?string $name): ?string
    {
        if (is_null($name) || ! str_contains($name, '.')) {
            return $name;
        }

        $parts = explode('.', $name);
        $result = '';

        foreach ($parts as $index => $part) {
            if ($index === 0) {
                $result .= $part;

                continue;
            }

            if ($part === '*') {
                $result .= '[]';

                continue;
            }

            $result .= "[{$part}]";
        }

        return $result;
    }

    public static function nameToDotted(?string $name): ?string
    {
        if (is_null($name) || str_contains($name, '.')) {
            return $name;
        }

        if ($name) {
            return str_replace(['[]', ']', '['], ['', '', '.'], $name);
        }

        return null;
    }

    public static function nameToDottedWithoutEmptyWildcard(?string $name): ?string
    {
        $dottedName = static::nameToDotted($name);

        if (str_contains($dottedName, '.*')) {
            $pattern = '/(.*)(\.\*)([^.]*$)/';
            $replacement = '$1$3';

            $dottedName = preg_replace($pattern, $replacement, $dottedName, 1);
        }

        return $dottedName;
    }

    public static function id(?string $id, ?string $bag = null): ?string
    {
        if ($id) {
            $shouldUseBag = $bag !== 'default' && ! is_null($bag);

            $id = str_replace('.', '_', self::nameToDotted($id));
            $id = str_replace('_*', '', $id);

            if ($shouldUseBag) {
                $id = "{$bag}_{$id}";
            }
        }

        return $id ?? null;
    }

    public static function selected(?string $name, mixed $default, $attributes): bool
    {
        if ($default instanceof Model) {
            $default = $default->getKey();
        }

        if ($default instanceof \UnitEnum) {
            $default = $default->value;
        }

        $value = filled($name) ? old($name, $default) : $default;

        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        // TODO: Check this block breaks anything
        if ($value === false) {
            return false;
        }

        return filled($value) && in_array((string) $attributes->get('value'), Arr::wrap($value));
    }

    public static function value(mixed $value): array|string|null
    {
        if ($value instanceof Moneyable) {
            return $value->format();
        }

        if (is_array($value)) {
            $value = collect($value);
        }

        return $value;
    }
}
