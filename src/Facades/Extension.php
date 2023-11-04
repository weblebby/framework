<?php

namespace Feadmin\Facades;

use Feadmin\Items\ExtensionItem;
use Feadmin\Managers\ExtensionManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(ExtensionItem $extension)
 * @method static void unregister(string $name)
 * @method static Collection<int, ExtensionItem> get()
 * @method static Collection<int, ExtensionItem> getAll()
 * @method static ExtensionItem|null findByName(string $name)
 * @method static ExtensionItem findByNameOrFail(string $name)
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
