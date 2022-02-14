<?php

use Feadmin\Facades\Localization;
use Feadmin\Facades\Preference;
use Illuminate\Support\Collection;

function t(
    string $key = null,
    string $group,
    array $replace = [],
    string $code = null,
): string|Collection {
    return Localization::get($key, $group, $replace, $code);
}

function preference(string|array $rawKey, mixed $default = null): mixed
{
    if (is_array($rawKey)) {
        return Preference::set($rawKey);
    }

    return Preference::get($rawKey, $default);
}
