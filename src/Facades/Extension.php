<?php

namespace Feadmin\Facades;

use Feadmin\Managers\ExtensionManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Feadmin\Abstracts\Extension\Extension register(string $extension)
 * @method static \Feadmin\Abstracts\Extension\Extension|null unregister(string $name)
 * @method static void loadRoutes()
 * @method static bool has(string $name, bool $onlyEnabled = true)
 * @method static Collection<int, \Feadmin\Abstracts\Extension\Extension> get()
 * @method static Collection<int, \Feadmin\Abstracts\Extension\Extension> getAll()
 * @method static \Feadmin\Abstracts\Extension\Extension|null findByName(string $name)
 * @method static \Feadmin\Abstracts\Extension\Extension findByNameOrFail(string $name)
 * @method static \Feadmin\Abstracts\Extension\Extension|null findByClass(string $class)
 * @method static \Feadmin\Abstracts\Extension\Extension findByClassOrFail(string $class)
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
