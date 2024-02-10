<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;
use Weblebby\Framework\Enums\InjectionTypeEnum;
use Weblebby\Framework\Managers\InjectionManager;

/**
 * @method static void add(InjectionTypeEnum|string|array $name, callable $callable)
 * @method static mixed call(InjectionTypeEnum|string $name, mixed $default = null)
 * @method static HtmlString render(InjectionTypeEnum|string $name)
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
