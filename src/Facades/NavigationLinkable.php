<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Weblebby\Framework\Items\NavigationLinkableItem;
use Weblebby\Framework\Managers\NavigationLinkableManager;

/**
 * @method static NavigationLinkableManager add(NavigationLinkableItem $data)
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
