<?php

namespace Feadmin;

use Feadmin\Facades\Extension;
use Feadmin\Hooks\Panel;
use Illuminate\Support\Facades\Route;

class Feadmin
{
    private array $panels = [];

    private ?string $currentPanel = null;

    private ?string $extensionPanel = null;

    public function create(string $panel): Panel
    {
        return $this->panels[$panel] = new Panel($panel);
    }

    public function find(?string $panel): ?Panel
    {
        return $this->panels[$panel] ?? null;
    }

    public function panels(): array
    {
        return $this->panels;
    }

    public function getCurrentPanel(): ?Panel
    {
        return $this->find($this->currentPanel);
    }

    public function setCurrentPanel(string $panel): void
    {
        $this->currentPanel = $panel;
    }

    public function getExtensionPanel(): ?Panel
    {
        return $this->find($this->extensionPanel);
    }

    public function setExtensionPanel(string $panel): void
    {
        $this->extensionPanel = $panel;
    }

    public function usePanelRoutes(): void
    {
        foreach ($this->panels() as $panel) {
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
                require __DIR__ . '/../routes/feadmin.php';
            });
        }
    }

    public function useExtensionRoutes(): void
    {
        Extension::get()->each(function ($extension) {
            $extension->routes();
        });
    }

    public function useWebRoutes(): void
    {
        Route::middleware('web')->group(__DIR__ . '/../routes/web.php');
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
