<?php

namespace Feadmin;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class Extension implements Arrayable
{
    protected array $attributes = [];

    public function __construct(string $name)
    {
        $this->id = Str::kebab($name);
        $this->name = $name;
        $this->is_enabled = !file_exists($this->path('.disabled'));
    }

    public function enable(): bool
    {
        $delete = File::delete($this->path('.disabled'));

        if ($delete !== true) {
            return false;
        }

        $this->is_enabled = true;
        $this->enabled();

        return $delete;
    }

    public function disable(): bool
    {
        $put = File::put($this->path('.disabled'), '');

        if ($put !== 0) {
            return false;
        }

        $this->is_enabled = false;
        $this->disabled();

        return $put;
    }

    public function migrate(string $method = null): void
    {
        if ($method) {
            $method = ":{$method}";
        }

        Artisan::call(
            'migrate' . $method,
            [
                '--path' => $this->originalPath('Database/Migrations'),
                '--force' => true,
            ]
        );
    }

    public function path(string $append = ''): string
    {
        return base_path($this->originalPath($append));
    }

    public function originalPath(string $append = ''): string
    {
        return "extensions/{$this->name}/{$append}";
    }

    public function asset(string $asset): string
    {
        return route('ext-asset', [$this->id, $asset]);
    }

    public function namespace(string $append = ''): string
    {
        return "Extensions\\{$this->name}\\{$append}";
    }

    public function title(string $singular, string $plural): self
    {
        $this->singular_title = $singular;
        $this->plural_title = $plural;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function category(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function routes(): void
    {
        //
    }

    public function register(): void
    {
        //
    }

    public function booting(): void
    {
        //
    }

    public function booted(): void
    {
        //
    }

    public function installed(): void
    {
        //
    }

    public function uninstalled(): void
    {
        //
    }

    public function enabled(): void
    {
        //
    }

    public function disabled(): void
    {
        //
    }
}
