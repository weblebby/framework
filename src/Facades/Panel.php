<?php

namespace Feadmin\Facades;

use Feadmin\Items\PanelItem;
use Feadmin\Managers\PanelManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PanelItem create(string $panel)
 * @method static PanelItem find(string $panel)
 * @method static array get()
 * @method static PanelItem getCurrentPanel()
 * @method static void setCurrentPanel(string $panel)
 * @method static PanelItem getExtensionPanel()
 * @method static void setExtensionPanel(string $panel)
 * @method static void usePanelRoutes()
 * @method static void useExtensionRoutes()
 * @method static void useWebRoutes()
 * @method static void useRoutes()
 * @method static string version()
 *
 * @see PanelManager
 */
class Panel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PanelManager::class;
    }
}
