<?php

namespace Feadmin\Facades;

use Feadmin\Managers\NavigationLinkableManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static NavigationLinkableManager add(array $data)
 * @method static Collection linkables()
 *
 * @see NavigationLinkableManager
 */
class NavigationLinkable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NavigationLinkableManager::class;
    }
}
