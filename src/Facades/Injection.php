<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;
use Weblebby\Framework\Managers\InjectionManager;

/**
 * @method static void add(string|array $name, callable $callable)
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
