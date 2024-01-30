<?php

namespace Weblebby\Framework\Concerns;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

trait HasViewAndRoutes
{
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
        return app()->joinPaths(rtrim($this->basePath(), DIRECTORY_SEPARATOR), $path);
    }

    public function vendorPath(?string $path = null): string
    {
        return app()->joinPaths(rtrim(resource_path("views/vendor/{$this->namespace()}"), DIRECTORY_SEPARATOR), $path);
    }

    public function view($view = null, $data = [], $mergeData = []): View
    {
        $key = str(get_parent_class($this))->classBasename()->camel()->toString();
        $mergeData = Arr::add($mergeData, $key, $this);

        return view($this->namespaceWith($view), $data, $mergeData);
    }

    public function route($name, $parameters = [], $absolute = true): string
    {
        return route($this->namespaceWith($name), $parameters, $absolute);
    }

    public function toRoute($route, $parameters = [], $status = 302, $headers = []): RedirectResponse
    {
        return redirect()->to($this->route($route, $parameters), $status, $headers);
    }

    public function routeIs(...$patterns): bool
    {
        $patterns = array_map(fn ($pattern) => $this->namespaceWith($pattern), $patterns);

        return request()->routeIs(...$patterns);
    }
}
