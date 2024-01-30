<?php

namespace Weblebby\Framework\Abstracts\Extension;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use JsonSerializable;
use Weblebby\Framework\Concerns\HasArray;
use Weblebby\Framework\Concerns\HasViewAndRoutes;
use Weblebby\Framework\Enums\ExtensionCategoryEnum;
use Weblebby\Framework\Facades\Panel;
use Weblebby\Framework\Services\ExtensionFileService;

abstract class Extension implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray, HasViewAndRoutes;

    protected ExtensionFileService $extensionFileService;

    protected ?ExtensionObserver $observerInstance = null;

    protected bool $isActive = true;

    abstract public function name(): string;

    abstract public function singularTitle(): string;

    abstract public function pluralTitle(): string;

    abstract public function description(): string;

    abstract public function category(): ExtensionCategoryEnum;

    abstract public function routes(): void;

    public function __construct()
    {
        $this->extensionFileService = new ExtensionFileService();
        $this->isActive = $this->extensionFileService->isExtensionActive($this);
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            return;
        }

        $this->isActive = true;
        $this->extensionFileService->activateExtension($this);

        $this->observer()?->activated();
    }

    public function deactivate(): void
    {
        if (! $this->isActive()) {
            return;
        }

        $this->isActive = false;
        $this->extensionFileService->deactivateExtension($this);

        $this->observer()?->deactivated();
    }

    /**
     * @return class-string<ExtensionObserver>|null
     */
    public function observerClass(): ?string
    {
        return null;
    }

    public function namespace(): string
    {
        return sprintf('ext-%s', $this->name());
    }

    public function migrate(bool $rollback = false): void
    {
        Artisan::call(sprintf('migrate%s', $rollback ? ':rollback' : ''), [
            '--path' => $this->path('database/migrations'),
            '--realpath' => true,
            '--force' => true,
        ]);
    }

    public function publish(): void
    {
        if (File::exists($targetPath = public_path("extensions/{$this->name()}"))) {
            return;
        }

        $sourcePath = $this->path('public');

        if (! File::exists($sourcePath)) {
            return;
        }

        // create extensions directory if not exists
        File::ensureDirectoryExists(public_path('extensions'));

        if (File::exists($targetPath)) {
            // delete symlink
            unlink($targetPath);
        }

        symlink($sourcePath, $targetPath);
    }

    public function registerRoute(string $route): void
    {
        $panel = Panel::getExtensionPanel();
        $path = $this->path(sprintf('routes/%s.php', $route));

        Route::middleware($panel->middlewareForPanel())
            ->prefix($panel->prefix().'/ext/'.$this->name())
            ->domain($panel->domain())
            ->as($this->namespaceWith())
            ->group($path);
    }

    public function observer(): ?ExtensionObserver
    {
        $class = $this->observerClass();

        if (is_null($class) || ! $this->isActive()) {
            return null;
        }

        if ($this->observerInstance) {
            return $this->observerInstance;
        }

        return $this->observerInstance = new $class($this);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'singular_title' => $this->singularTitle(),
            'plural_title' => $this->pluralTitle(),
            'description' => $this->description(),
            'category' => $this->category(),
            'path' => $this->path(),
            'is_active' => $this->isActive(),
            'observer' => $this->observer(),
        ];
    }
}
