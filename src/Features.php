<?php

namespace Feadmin;

use Feadmin\Facades\Feadmin;

class Features
{
    public static function enabled(string $feature, string $panel): bool
    {
        $features = Feadmin::panels($panel)->features();

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
