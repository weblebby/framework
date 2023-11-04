<?php

namespace Feadmin\Items;

use Feadmin\Concerns\ExtensionObserver;
use Feadmin\Enums\ExtensionCategoryEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Artisan;
use JsonSerializable;

class ExtensionItem implements Arrayable, JsonSerializable, Jsonable
{
    protected string $name;

    protected string $singularTitle;

    protected string $pluralTitle;

    protected string $description;

    protected ExtensionCategoryEnum $category;

    protected string $path;

    protected array $routes = [];

    protected bool $isActive = true;

    protected ?string $observer = null;

    protected ?ExtensionObserver $observerInstance = null;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setTitle(string $singular, string $plural): self
    {
        $this->singularTitle = $singular;
        $this->pluralTitle = $plural;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setCategory(ExtensionCategoryEnum $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function setRoutes(array $routes): self
    {
        $this->routes = $routes;

        return $this;
    }

    public function setObserver(string $observer): self
    {
        $this->observer = $observer;

        return $this;
    }

    public function setActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function activate(): self
    {
        $this->isActive = true;

        return $this;
    }

    public function deactivate(): self
    {
        $this->isActive = false;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function singularTitle(): string
    {
        return $this->singularTitle;
    }

    public function pluralTitle(): string
    {
        return $this->pluralTitle;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function category(): ExtensionCategoryEnum
    {
        return $this->category;
    }

    public function path(string $path = null): string
    {
        return $this->path . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    public function routes(): array
    {
        return $this->routes;
    }

    public function observer(): ?ExtensionObserver
    {
        if ($this->observerInstance) {
            return $this->observerInstance;
        }

        if ($this->observer) {
            $this->observerInstance = new $this->observer($this);

            return $this->observerInstance;
        }

        return null;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    protected function migrate(string $method): void
    {
        $method = match ($method) {
            'down' => ':rollback',
            default => '',
        };

        Artisan::call("migrate{$method}", [
            '--path' => $this->path('database/migrations'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'singular_title' => $this->singularTitle,
            'plural_title' => $this->pluralTitle,
            'description' => $this->description,
            'category' => $this->category->value,
            'path' => $this->path,
            'routes' => $this->routes,
            'is_active' => $this->isActive,
        ];
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
