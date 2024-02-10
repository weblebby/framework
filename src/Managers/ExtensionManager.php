<?php

namespace Weblebby\Framework\Managers;

use Illuminate\Support\Collection;
use Weblebby\Framework\Abstracts\Extension\Extension;

class ExtensionManager
{
    /**
     * @var Collection<int, Extension>
     */
    protected Collection $extensions;

    public function __construct()
    {
        $this->extensions = collect();
    }

    /**
     * @param  class-string<Extension>  $extension
     */
    public function register(string $extension): Extension
    {
        $extension = new $extension();

        $this->extensions->push($extension);

        return $extension;
    }

    public function unregister(string $name): ?Extension
    {
        $this->extensions = $this->extensions->reject(
            fn (Extension $extension) => $extension->name() === $name
        );

        return $this->findByName($name);
    }

    public function loadRoutes(): void
    {
        $this->get()->each(function (Extension $extension) {
            $extension->routes();
        });
    }

    public function observeRegister(): void
    {
        $this->get()->each(function (Extension $extension) {
            $extension->observer()?->register();
        });
    }

    public function observeAfterPanelBoot(): void
    {
        $this->get()->each(function (Extension $extension) {
            $extension->observer()?->afterPanelBoot();
        });
    }

    public function observeBoot(): void
    {
        $this->get()->each(function (Extension $extension) {
            $extension->observer()?->boot();
        });
    }

    /**
     * @param  string|class-string<Extension>  $name
     */
    public function has(string $name, bool $onlyEnabled = true): bool
    {
        $extensions = $onlyEnabled ? $this->get() : $this->getWithDeactivated();

        if (class_exists($name)) {
            return $extensions->contains(fn (Extension $extension) => $extension instanceof $name);
        }

        return $extensions->contains(fn (Extension $extension) => $extension->name() === $name);
    }

    /**
     * @return Collection<int, Extension>
     */
    public function get(): Collection
    {
        return $this->extensions->where(fn (Extension $extension) => $extension->isActive())->values();
    }

    /**
     * @return Collection<int, Extension>
     */
    public function getWithDeactivated(): Collection
    {
        return $this->extensions;
    }

    public function findByName(string $name): ?Extension
    {
        return $this->extensions->firstWhere('name', $name);
    }

    public function findByNameOrFail(string $name): Extension
    {
        return $this->extensions->where('name', $name)->firstOrFail();
    }

    /**
     * @param  class-string<Extension>  $class
     */
    public function findByClass(string $class): ?Extension
    {
        return $this->extensions
            ->filter(fn (Extension $extension) => $extension instanceof $class)
            ->first();
    }

    /**
     * @param  class-string<Extension>  $class
     */
    public function findByClassOrFail(string $class): Extension
    {
        return $this->extensions
            ->filter(fn (Extension $extension) => $extension instanceof $class)
            ->firstOrFail();
    }
}
