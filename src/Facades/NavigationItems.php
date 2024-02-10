<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Weblebby\Framework\Managers\NavigationItemsManager;

/**
 * @method static Collection get(string $handle)
 *
 * @see NavigationItemsManager
 */
class NavigationItems extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NavigationItemsManager::class;
    }
}
