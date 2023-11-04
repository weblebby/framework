<?php

namespace Feadmin\Managers;

use Feadmin\Items\ExtensionItem;
use Illuminate\Support\Collection;

class ExtensionManager
{
    /**
     * @var Collection<int, ExtensionItem> $extensions
     */
    protected Collection $extensions;

    public function __construct()
    {
        $this->extensions = collect();
    }

    public function register(ExtensionItem $extension): void
    {
        $this->extensions->push($extension);
    }

    public function unregister(string $name): void
    {
        $this->extensions = $this->extensions->reject(
            fn(ExtensionItem $extension) => $extension->name() === $name
        );
    }

    /**
     * @return Collection<int, ExtensionItem>
     */
    public function get(): Collection
    {
        return $this->extensions->where('is_active', true);
    }

    /**
     * @return Collection<int, ExtensionItem>
     */
    public function getAll(): Collection
    {
        return $this->extensions;
    }

    public function findByName(string $name): ?ExtensionItem
    {
        return $this->extensions->firstWhere('name', $name);
    }

    public function findByNameOrFail(string $name): ExtensionItem
    {
        return $this->extensions->where('name', $name)->firstOrFail();
    }
}
