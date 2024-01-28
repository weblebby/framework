<?php

namespace Feadmin\Managers;

use Exception;
use Feadmin\Exceptions\PanelNotFoundException;
use Feadmin\Facades\Extension;
use Feadmin\Items\PanelItem;
use Illuminate\Support\Facades\Route;

class PanelManager
{
    const API_ROUTE_NAME_PREFIX = 'panel::api.';

    private array $panels = [];

    private ?string $currentPanel = null;

    private ?string $extensionPanel = null;

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * @throws Exception
     */
    public function create(string $panel): PanelItem
    {
        if (isset($this->panels[$panel])) {
            throw new Exception(sprintf('Panel [%s] already exists.', $panel));
        }

        return $this->panels[$panel] = new PanelItem($panel);
    }

    public function find(?string $panel): ?PanelItem
    {
        return $this->panels[$panel] ?? null;
    }

    public function findOrFail(string $panel): PanelItem
    {
        return $this->find($panel) ?? abort(404);
    }

    /**
     * @return array<int, PanelItem>
     */
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

    /**
     * @throws PanelNotFoundException
     */
    public function setExtensionPanel(string $panel): void
    {
        if (! $this->find($panel)) {
            throw new PanelNotFoundException($panel);
        }

        $this->extensionPanel = $panel;
    }

    public function usePanelRoutes(): void
    {
        foreach ($this->get() as $panel) {
            $routePaths = [
                __DIR__.'/../../routes/panel.php',
                ...$panel->routePaths(),
            ];

            foreach ($routePaths as $routePath) {
                $this->loadRoutesFrom(
                    path: $routePath,
                    panel: $panel,
                    middleware: $panel->middlewareForPanel(),
                );
            }
        }
    }

    public function useFortifyRoutes(): void
    {
        foreach ($this->get() as $panel) {
            $this->loadRoutesFrom(
                path: __DIR__.'/../../routes/fortify.php',
                panel: $panel,
                middleware: $panel->middlewareForFortify(),
            );
        }
    }

    public function useExtensionRoutes(): void
    {
        Extension::loadRoutes();
    }

    public function useWebRoute(?array $middlewares = null): void
    {
        Route::middleware($middlewares ?? 'web')->group(__DIR__.'/../../routes/web.php');
    }

    public function useApiRoute(?array $middlewares = null): void
    {
        Route::middleware($middlewares ?? ['api', 'auth:sanctum'])
            ->as(self::API_ROUTE_NAME_PREFIX)
            ->group(__DIR__.'/../../routes/api.php');
    }

    public function useRoutes(): void
    {
        $this->usePanelRoutes();
        $this->useExtensionRoutes();
        $this->useWebRoute();
        $this->useApiRoute();
    }

    protected function loadRoutesFrom(
        string $path,
        PanelItem $panel,
        string|array|null $middleware = null
    ): void {
        $route = Route::middleware($middleware ?? $panel->middlewareForPanel());

        if ($panel->prefix()) {
            $route->prefix($panel->prefix());
        }

        if ($panel->as()) {
            $route->as($panel->as());
        }

        if ($panel->domain()) {
            $route->domain($panel->domain());
        }

        $route->group(function () use ($panel, $path) {
            require $path;
        });
    }
}
