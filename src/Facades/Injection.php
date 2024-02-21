<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;
use Weblebby\Framework\Enums\InjectionTypeEnum;
use Weblebby\Framework\Managers\InjectionManager;

/**
 * @method static void has(InjectionTypeEnum|string $key)
 * @method static void add(InjectionTypeEnum|string|array $key, mixed $value)
 * @method static mixed call(InjectionTypeEnum|string $key, mixed $default = null)
 * @method static HtmlString render(InjectionTypeEnum|string $key)
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
