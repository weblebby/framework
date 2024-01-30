<?php

use Illuminate\Http\RedirectResponse;
use Weblebby\Framework\Facades\Panel;
use Weblebby\Framework\Facades\Preference;
use Weblebby\Framework\Items\PanelItem;
use Weblebby\Framework\Managers\PanelManager;

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

function domain(): ?\Weblebby\Core\Models\Domain
{
    if (class_exists(\Weblebby\Core\Facades\Domain::class)) {
        return \Weblebby\Core\Facades\Domain::getDomain();
    }

    return null;
}
