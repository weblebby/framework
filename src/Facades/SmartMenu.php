<?php

namespace Feadmin\Facades;

use Feadmin\Items\SmartMenuItem;
use Feadmin\Managers\SmartMenuManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

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
