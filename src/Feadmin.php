<?php

namespace Feadmin;

use Feadmin\Hooks\PanelHook;
use Illuminate\Support\Facades\Route;

class Feadmin
{
    private array $panels = [];

    private string $currentPanel;

    public function currentPanel(string $panel = null): string|PanelHook
    {
        if (filled($panel)) {
            $this->currentPanel = $panel;
        }

        return $this->panels[$this->currentPanel];
    }

    public function panels(string $panel = null): PanelHook|array
    {
        if (is_null($panel)) {
            return $this->panels;
        }

        return $this->panels[$panel] ??= new PanelHook($panel);
    }

    public function usePanelRoutes()
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

    public function version(): string
    {
        return '1.0.0';
    }
}
