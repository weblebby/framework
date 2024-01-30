<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Weblebby\Framework\Items\SmartMenuItem;
use Weblebby\Framework\Managers\SmartMenuManager;

/**
 * @method static SmartMenuManager add(SmartMenuItem $data)
 * @method static Collection items()
 *
 * @see SmartMenuManager
 */
class SmartMenu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SmartMenuManager::class;
    }
}
