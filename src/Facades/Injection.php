<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void add(string $name, callable $callable)
 * @method static \Illuminate\Support\HtmlString render(string $name)
 * 
 * @see \Feadmin\Services\InjectionService
 */
class Injection extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\InjectionService::class;
    }
}
