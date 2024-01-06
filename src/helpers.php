<?php

use Feadmin\Facades\Panel;
use Feadmin\Facades\Preference;
use Feadmin\Items\PanelItem;
use Feadmin\Managers\PanelManager;
use Illuminate\Http\RedirectResponse;

function panel(?string $panel = null): ?PanelItem
{
    if (is_null($panel)) {
        return Panel::getCurrentPanel();
    }

    return Panel::find($panel);
}

function preference(string|array $rawKey, mixed $default = null, ?string $locale = null, array $options = []): mixed
{
    if (is_array($rawKey)) {
        return Preference::set($rawKey, $locale, $options);
    }

    return Preference::get($rawKey, $default, $locale);
}

function panel_route($name, $parameters = [], $absolute = true): string
{
    return panel()->route($name, $parameters, $absolute);
}

function panel_api_route($name, $parameters = [], $absolute = true): string
{
    return route(PanelManager::API_ROUTE_NAME_PREFIX.$name, $parameters, $absolute);
}

function to_panel_route($route, $parameters = [], $status = 302, $headers = []): RedirectResponse
{
    return panel()->toRoute($route, $parameters, $status, $headers);
}
