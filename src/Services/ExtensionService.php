<?php

namespace Feadmin\Services;

use Feadmin\Extension;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class ExtensionService
{
    private Collection $extensions;

    public function registerAllExtensions(): void
    {
        $extensions = File::directories(app_path('Extensions'));

        $this->extensions = collect($extensions)->map(function ($extension) {
            return $this->getExtension($extension);
        });
    }

    public function get(): Collection
    {
        return $this->extensions->where('is_enabled', true);
    }

    public function all(): Collection
    {
        return $this->extensions;
    }

    public function has(): bool
    {
        return $this->extensions->where('id', 'form')->isNotEmpty();
    }

    private function getExtension(string $directory): Extension
    {
        $name = basename($directory);
        $namespace = $this->getNamespace($name);

        $extension = new $namespace($name);

        $extension->register();

        if ($extension->is_enabled) {
            $extension->booting();
            $this->registerViews($extension);
        }

        return $extension;
    }

    private function getNamespace(string $directory): string
    {
        return "App\\Extensions\\{$directory}\\{$directory}Extension";
    }

    public function registerRoutes(Extension $extension): void
    {
        if (in_array('web', $extension->routes())) {
            Route::middleware('web')
                ->name("{$extension->id}::")
                ->group($extension->path('Routes/web.php'));
        }

        /**
         * TODO: Middleware for admin routes
         */
        if (in_array('admin', $extension->routes())) {
            Route::middleware(['web', 'auth'])
                ->prefix('admin')
                ->name("{$extension->id}::admin.")
                ->group($extension->path('Routes/admin.php'));
        }
    }

    private function registerViews(Extension $extension): void
    {
        View::addNamespace($extension->id, $extension->path('Resources/views'));
    }
}
