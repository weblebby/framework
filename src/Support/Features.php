<?php

namespace Feadmin\Support;

use Feadmin\Facades\Panel;
use Feadmin\Items\PanelItem;

class Features
{
    public static function enabled(string $feature, PanelItem|string|null $panel): bool
    {
        if (is_null($panel)) {
            return false;
        }

        $features = $panel instanceof PanelItem
            ? $panel->features()
            : Panel::find($panel)->features();

        return in_array($feature, $features);
    }

    public static function navigations(): string
    {
        return 'navigations';
    }

    public static function extensions(): string
    {
        return 'extensions';
    }

    public static function translations(): string
    {
        return 'translations';
    }

    public static function preferences(): string
    {
        return 'preferences';
    }

    public static function users(): string
    {
        return 'users';
    }

    public static function roles(): string
    {
        return 'roles';
    }

    public static function posts(): string
    {
        return 'posts';
    }
}
