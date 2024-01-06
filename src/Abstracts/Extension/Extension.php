<?php

namespace Feadmin\Abstracts\Extension;

use ArrayAccess;
use Feadmin\Concerns\HasArray;
use Feadmin\Enums\ExtensionCategoryEnum;
use Feadmin\Facades\Panel;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use JsonSerializable;

abstract class Extension implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use HasArray;

    protected ?ExtensionObserver $observerInstance = null;

    protected bool $isActive = true;

    abstract public function name(): string;

    abstract public function singularTitle(): string;

    abstract public function pluralTitle(): string;

    abstract public function description(): string;

    abstract public function category(): ExtensionCategoryEnum;

    abstract public function routes(): void;

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
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

    public function namespaceWith(string $append = ''): string
    {
        return sprintf('%s::%s', $this->namespace(), $append);
    }

    public function basePath(): string
    {
        $filename = (new \ReflectionClass($this))->getFileName();

        return dirname($filename).'/../';
    }

    public function path(?string $path = null): string
    {
        $path = sprintf('%s/%s', $this->basePath(), ltrim($path, '/'));

        return rtrim($path, '/');
    }

    public function view($view = null, $data = [], $mergeData = [])
    {
        $mergeData = Arr::add($mergeData, 'extension', $this);

        return view($this->namespaceWith($view), $data, $mergeData);
    }

    public function route($name, $parameters = [], $absolute = true)
    {
        return route($this->namespaceWith($name), $parameters, $absolute);
    }

    public function toRoute($route, $parameters = [], $status = 302, $headers = [])
    {
        return redirect()->to($this->route($route, $parameters), $status, $headers);
    }

    public function routeIs($name): bool
    {
        return request()->routeIs($this->namespaceWith($name));
    }

    public function migrate(bool $rollback = false): void
    {
        Artisan::call(sprintf('migrate%s', $rollback ? ':rollback' : ''), [
            '--path' => $this->path('database/migrations'),
            '--force' => true,
        ]);
    }

    public function registerRoute(string $route): void
    {
        $panel = Panel::getExtensionPanel();
        $path = $this->path(sprintf('routes/%s.php', $route));

        Route::middleware($panel->middleware())
            ->prefix($panel->prefix())
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
            'routes' => $this->routes(),
            'is_active' => $this->isActive(),
            'observer' => $this->observer(),
        ];
    }
}
