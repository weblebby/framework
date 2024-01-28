<?php

namespace Feadmin\Facades;

use Feadmin\Managers\ThemeManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Feadmin\Abstracts\Theme\Theme register(\Feadmin\Abstracts\Theme\Theme|string $theme)
 * @method static ThemeManager activate(string $themeName)
 * @method static Collection<int, \Feadmin\Abstracts\Theme\Theme> get()
 * @method static \Feadmin\Abstracts\Theme\Theme find(string $themeName)
 * @method static \Feadmin\Abstracts\Theme\Theme|null active()
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
