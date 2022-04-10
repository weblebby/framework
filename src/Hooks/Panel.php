<?php

namespace Feadmin\Hooks;

use Illuminate\Http\RedirectResponse;

class Panel
{
    private string $name;

    private Menu $menu;

    private PreferenceBag $preference;

    private Permission $permission;

    private array $features = [];

    private ?string $prefix = null;

    private ?string $as = null;

    private ?string $domain = null;

    private string|array|null $middleware = null;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->menu = new Menu();
        $this->preference = new PreferenceBag();
        $this->permission = new Permission();
    }

    public function menu(string $location = null): Menu
    {
        if (is_null($location)) {
            return $this->menu;
        }

        return $this->menu->location($location);
    }

    public function preference(string $namespace = null): PreferenceBag
    {
        if (is_null($namespace)) {
            return $this->preference;
        }

        return $this->preference->namespace($namespace);
    }

    public function permission(string $group = null): Permission
    {
        if (is_null($group)) {
            return $this->permission;
        }

        return $this->permission->group($group);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function route($name, $parameters = [], $absolute = true): string
    {
        return route($this->as() . $name, $parameters, $absolute);
    }

    public function toRoute($route, $parameters = [], $status = 302, $headers = []): RedirectResponse
    {
        return redirect()->route($this->as() . $route, $parameters, $status, $headers);
    }

    public function features(array $features = null): self|array
    {
        if (is_null($features)) {
            return $this->features;
        }

        $this->features = $features;

        return $this;
    }

    public function prefix(string $prefix = null): self|string|null
    {
        if (is_null($prefix)) {
            return $this->prefix;
        }

        $this->prefix = $prefix;

        return $this;
    }

    public function as(string $as = null): self|string|null
    {
        if (is_null($as)) {
            return $this->as;
        }

        $this->as = $as;

        return $this;
    }

    public function domain(string $domain = null): self|string|null
    {
        if (is_null($domain)) {
            return $this->domain;
        }

        $this->domain = $domain;

        return $this;
    }

    public function middleware(string|array $middleware = null): self|string|array|null
    {
        if (is_null($middleware)) {
            return $this->middleware;
        }

        $this->middleware = $middleware;

        return $this;
    }
}
