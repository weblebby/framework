<?php

namespace Feadmin\Services;

use Feadmin\Extension;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ExtensionService
{
    private Collection $extensions;

    public function __construct()
    {
        $this->extensions = collect();
    }

    public function works(): bool
    {
        return file_exists($this->path());
    }

    public function start(): void
    {
        if (!$this->works()) {
            return;
        }

        $extensions = File::directories($this->path());

        $this->extensions = collect($extensions)->map(function ($extension) {
            return $this->getExtension($extension);
        });
    }

    public function enabled(): Collection
    {
        return $this->extensions->where('is_enabled', true);
    }

    public function get(): Collection
    {
        return $this->extensions;
    }

    public function path(): string
    {
        return base_path('extensions');
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
        return "Extensions\\{$directory}\\{$directory}Extension";
    }

    private function registerViews(Extension $extension): void
    {
        View::addNamespace("ext-{$extension->id}", $extension->path('Resources/views'));
    }
}
