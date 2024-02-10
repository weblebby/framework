<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Weblebby\Framework\Managers\ThemeManager;

/**
 * @method static \Weblebby\Framework\Abstracts\Theme\Theme register(\Weblebby\Framework\Abstracts\Theme\Theme|string $theme)
 * @method static ThemeManager activate(string $themeName)
 * @method static Collection<int, \Weblebby\Framework\Abstracts\Theme\Theme> get()
 * @method static \Weblebby\Framework\Abstracts\Theme\Theme|null find(string $themeName)
 * @method static \Weblebby\Framework\Abstracts\Theme\Theme findOrFail(string $themeName)
 * @method static \Weblebby\Framework\Abstracts\Theme\Theme|null active()
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
