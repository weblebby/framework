<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self add(array $data)
 * @method static \Illuminate\Support\Collection linkables()
 * 
 * @see \Feadmin\Services\NavigationLinkableService
 */
class NavigationLinkable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\NavigationLinkableService::class;
    }
}
