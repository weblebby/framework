<?php

use Illuminate\Http\RedirectResponse;
use Weblebby\Framework\Abstracts\Theme\Theme as ThemeAbstract;
use Weblebby\Framework\Facades\Panel;
use Weblebby\Framework\Facades\Preference;
use Weblebby\Framework\Facades\Theme;
use Weblebby\Framework\Items\PanelItem;
use Weblebby\Framework\Managers\PanelManager;
use Weblebby\Framework\Support\Once;
use Weblebby\Framework\Support\Onceable;

function panel(?string $panel = null): ?PanelItem
{
    if (is_null($panel)) {
        return Panel::getCurrentPanel();
    }

    return Panel::find($panel);
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

function panel_build_dir(): string
{
    return 'st-weblebby';
}

function panel_build_path(): string
{
    return sprintf('%s/build', panel_build_dir());
}

function theme(): ?ThemeAbstract
{
    return Theme::active();
}

function preference(string|array $rawKey, mixed $default = null, ?string $locale = null, array $options = []): mixed
{
    if (is_array($rawKey)) {
        return Preference::set($rawKey, $locale, $options);
    }

    return Preference::get($rawKey, $default, $locale);
}

function extensions_build_dir(): string
{
    return 'st-extensions';
}

function extension_asset_path(string $extension): string
{
    return sprintf('%s/%s', extensions_build_dir(), $extension);
}

function extension_build_path(string $extension): string
{
    return extension_asset_path($extension).'/build';
}

function themes_build_dir(): string
{
    return 'st-themes';
}

function theme_asset_path(string $theme): string
{
    return sprintf('%s/%s', themes_build_dir(), $theme);
}

function theme_build_path(string $theme): string
{
    return theme_asset_path($theme).'/build';
}

/**
 * Ensures a callable is only called once, and returns the result on subsequent calls.
 *
 * @template  TReturnType
 *
 * @param  callable(): TReturnType  $callback
 * @return TReturnType
 */
function once(callable $callback)
{
    $onceable = Onceable::tryFromTrace(
        debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2),
        $callback,
    );

    return $onceable ? Once::instance()->value($onceable) : call_user_func($callback);
}
