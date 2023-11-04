<?php

namespace Feadmin\Facades;

use Feadmin\Managers\InjectionManager;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;

/**
 * @method static void add(string $name, callable $callable)
 * @method static HtmlString render(string $name)
 *
 * @see InjectionManager
 */
class Injection extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return InjectionManager::class;
    }
}
