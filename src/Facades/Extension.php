<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Weblebby\Framework\Managers\ExtensionManager;

/**
 * @method static \Weblebby\Framework\Abstracts\Extension\Extension register(string $extension)
 * @method static \Weblebby\Framework\Abstracts\Extension\Extension|null unregister(string $name)
 * @method static void loadRoutes()
 * @method static void observeRegister()
 * @method static void observeAfterPanelBoot()
 * @method static void observeBoot()
 * @method static bool has(string $name, bool $onlyEnabled = true)
 * @method static Collection<int, \Weblebby\Framework\Abstracts\Extension\Extension> get()
 * @method static Collection<int, \Weblebby\Framework\Abstracts\Extension\Extension> getWithDeactivated()
 * @method static \Weblebby\Framework\Abstracts\Extension\Extension|null findByName(string $name)
 * @method static \Weblebby\Framework\Abstracts\Extension\Extension findByNameOrFail(string $name)
 * @method static \Weblebby\Framework\Abstracts\Extension\Extension|null findByClass(string $class)
 * @method static \Weblebby\Framework\Abstracts\Extension\Extension findByClassOrFail(string $class)
 *
 * @see ExtensionManager
 */
class Extension extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ExtensionManager::class;
    }
}
