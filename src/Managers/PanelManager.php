<?php

namespace Feadmin\Managers;

use Feadmin\Facades\Extension;
use Feadmin\Items\ExtensionItem;
use Feadmin\Items\PanelItem;
use Illuminate\Support\Facades\Route;

class PanelManager
{
    private array $panels = [];

    private ?string $currentPanel = null;

    private ?string $extensionPanel = null;

    public function create(string $panel): PanelItem
    {
        return $this->panels[$panel] = new PanelItem($panel);
    }

    public function find(?string $panel): ?PanelItem
    {
        return $this->panels[$panel] ?? null;
    }

    public function get(): array
    {
        return $this->panels;
    }

    public function getCurrentPanel(): ?PanelItem
    {
        return $this->find($this->currentPanel);
    }

    public function setCurrentPanel(string $panel): void
    {
        $this->currentPanel = $panel;
    }

    public function getExtensionPanel(): ?PanelItem
    {
        return $this->find($this->extensionPanel);
    }

    public function setExtensionPanel(string $panel): void
    {
        $this->extensionPanel = $panel;
    }

    public function usePanelRoutes(): void
    {
        foreach ($this->get() as $panel) {
            $route = Route::middleware($panel->middleware());

            if ($panel->prefix()) {
                $route->prefix($panel->prefix());
            }

            if ($panel->as()) {
                $route->as($panel->as());
            }

            if ($panel->domain()) {
                $route->domain($panel->domain());
            }

            $route->group(function () use ($panel) {
                require __DIR__ . '/../../routes/panel.php';
            });
        }
    }

    public function useExtensionRoutes(): void
    {
        Extension::get()->each(function (ExtensionItem $extension) {
            $extension->routes();
        });
    }

    public function useWebRoutes(): void
    {
        Route::middleware('web')->group(__DIR__ . '/../../routes/web.php');
    }

    public function useRoutes(): void
    {
        $this->usePanelRoutes();
        $this->useExtensionRoutes();
        $this->useWebRoutes();
    }

    public function version(): string
    {
        return '1.0.0';
    }
}
