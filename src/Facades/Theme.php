<?php

namespace Feadmin\Facades;

use Feadmin\Managers\ThemeManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ThemeManager register(\Feadmin\Concerns\Theme\Theme|string $theme)
 * @method static ThemeManager activate(string $themeName)
 * @method static Collection<int, \Feadmin\Concerns\Theme\Theme> get()
 * @method static \Feadmin\Concerns\Theme\Theme find(string $themeName)
 * @method static \Feadmin\Concerns\Theme\Theme active()
 *
 * @see ThemeManager
 */
class Theme extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ThemeManager::class;
    }
}
