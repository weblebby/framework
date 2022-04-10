<?php

use Feadmin\Facades\Feadmin;
use Feadmin\Facades\Localization;
use Feadmin\Facades\Preference;
use Feadmin\Hooks\Panel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

function t(
    string $key = null,
    string $group,
    array $replace = [],
    string $code = null,
): string|Collection {
    return Localization::get($key, $group, $replace, $code);
}

function panel(string $panel = null): Panel
{
    if (is_null($panel)) {
        return Feadmin::getCurrentPanel();
    }

    return Feadmin::find($panel);
}

function preference(string|array $rawKey, mixed $default = null): mixed
{
    if (is_array($rawKey)) {
        return Preference::set($rawKey);
    }

    return Preference::get($rawKey, $default);
}

function panel_route($name, $parameters = [], $absolute = true): string
{
    return panel()->route($name, $parameters, $absolute);
}

function to_panel_route($route, $parameters = [], $status = 302, $headers = []): RedirectResponse
{
    return panel()->toRoute($route, $parameters, $status, $headers);
}
