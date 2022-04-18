<?php

namespace Feadmin;

use Feadmin\Facades\Feadmin;
use Feadmin\Hooks\Panel;

class Features
{
    public static function enabled(string $feature, Panel|string $panel): bool
    {
        $features = $panel instanceof Panel
            ? $panel->features()
            : Feadmin::find($panel)->features();

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
}
