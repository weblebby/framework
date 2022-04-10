<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void boot()
 * @method static \Illuminate\Support\Collection enabled()
 * @method static \Illuminate\Support\Collection get()
 * 
 * @see \Feadmin\Services\ExtensionService
 */
class Extension extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\ExtensionService::class;
    }
}
