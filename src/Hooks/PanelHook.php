<?php

namespace Feadmin\Hooks;

class PanelHook
{
    private string $name;

    private MenuHook $menuHook;

    private PreferenceGroupHook $preferenceHook;

    private PermissionHook $permissionHook;

    private array $features = [];

    private ?string $prefix = null;

    private ?string $as = null;

    private ?string $domain = null;

    private string|array|null $middleware = null;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->menuHook = new MenuHook();
        $this->preferenceHook = new PreferenceGroupHook();
        $this->permissionHook = new PermissionHook();
    }

    public function menus(string $location = null): MenuHook
    {
        if (filled($location)) {
            $this->menuHook->location($location);
        }

        return $this->menuHook;
    }

    public function preferences(string $namespace = null): PreferenceGroupHook
    {
        if (filled($namespace)) {
            $this->preferenceHook->namespace($namespace);
        }

        return $this->preferenceHook;
    }

    public function permissions(string $group = null): PermissionHook
    {
        if (filled($group)) {
            $this->permissionHook->group($group);
        }

        return $this->permissionHook;
    }

    public function name(): string
    {
        return $this->name;
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
