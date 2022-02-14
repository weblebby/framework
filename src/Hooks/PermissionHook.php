<?php

namespace Feadmin\Hooks;

class PermissionHook
{
    private string $lastGroup;

    private array $permissions = [];

    public function group(string $group): self
    {
        $this->lastGroup = $group;
        $this->permissions[$this->lastGroup] ??= [];

        return $this;
    }

    public function title(string $title): self
    {
        $this->permissions[$this->lastGroup]['title'] = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->permissions[$this->lastGroup]['description'] = $description;

        return $this;
    }

    public function permissions(array $permissions): self
    {
        $this->permissions[$this->lastGroup]['permissions'] = $permissions;

        return $this;
    }

    public function get(): array
    {
        return $this->permissions;
    }

    public function keys(): array
    {
        return collect($this->get())
            ->map(function ($group, $groupKey) {
                return collect($group['permissions'])
                    ->map(fn ($_, $permKey) => "{$groupKey}:{$permKey}")
                    ->values();
            })
            ->collapse()
            ->toArray();
    }
}
