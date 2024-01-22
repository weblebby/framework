<?php

namespace Feadmin\Items;

use Feadmin\Hooks\MenuHook;
use Feadmin\Hooks\PermissionHook;
use Feadmin\Hooks\PreferenceBagHook;
use Illuminate\Http\RedirectResponse;

class PanelItem
{
    protected string $name;

    protected MenuHook $menu;

    protected PreferenceBagHook $preference;

    protected PermissionHook $permission;

    protected array $features = [];

    protected ?string $prefix = null;

    protected ?string $as = null;

    protected ?string $domain = null;

    protected string|array|null $middleware = null;

    protected array $routePaths = [];

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->menu = new MenuHook();
        $this->preference = new PreferenceBagHook();
        $this->permission = new PermissionHook($this);
    }

    public function menu(?string $bag = null): MenuHook
    {
        if (is_null($bag)) {
            return $this->menu;
        }

        return $this->menu->withBag($bag);
    }

    public function preference(?string $namespace = null): PreferenceBagHook
    {
        if (is_null($namespace)) {
            return $this->preference;
        }

        return $this->preference->withNamespace($namespace);
    }

    public function permission(?string $group = null): PermissionHook
    {
        if (is_null($group)) {
            return $this->permission;
        }

        return $this->permission->withGroup($group);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function nameWith(string $append): string
    {
        return sprintf('%s::%s', $this->name(), $append);
    }

    public function route($name, $parameters = [], $absolute = true): string
    {
        return route($this->as().$name, $parameters, $absolute);
    }

    public function routeIs(...$patterns): bool
    {
        $patterns = array_map(fn ($pattern) => $this->as().$pattern, $patterns);

        return request()->routeIs(...$patterns);
    }

    public function toRoute($route, $parameters = [], $status = 302, $headers = []): RedirectResponse
    {
        return to_route($this->as().$route, $parameters, $status, $headers);
    }

    public function features(?array $features = null): self|array
    {
        if (is_null($features)) {
            return $this->features;
        }

        $this->features = $features;

        return $this;
    }

    public function prefix(?string $prefix = null): self|string|null
    {
        if (is_null($prefix)) {
            return $this->prefix;
        }

        $this->prefix = $prefix;

        return $this;
    }

    public function as(?string $as = null): self|string|null
    {
        if (is_null($as)) {
            return $this->as;
        }

        $this->as = $as;

        return $this;
    }

    public function domain(?string $domain = null): self|string|null
    {
        if (is_null($domain)) {
            return $this->domain;
        }

        $this->domain = $domain;

        return $this;
    }

    public function middleware(string|array|null $middleware = null): self|string|array|null
    {
        if (is_null($middleware)) {
            return $this->middleware;
        }

        $this->middleware = $middleware;

        return $this;
    }

    public function loadRoutesFrom(string $path): self
    {
        $this->routePaths[] = $path;

        return $this;
    }

    public function routePaths(): array
    {
        return $this->routePaths;
    }
}
