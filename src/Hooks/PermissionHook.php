<?php

namespace Weblebby\Framework\Hooks;

use Weblebby\Framework\Items\PanelItem;

class PermissionHook
{
    protected string $lastGroup;

    protected array $permissions = [];

    public function __construct(protected PanelItem $panel)
    {
        //
    }

    public function withGroup(string $group): self
    {
        $this->lastGroup = $group;
        $this->permissions[$this->lastGroup] ??= [];

        return $this;
    }

    public function withTitle(string $title): self
    {
        $this->permissions[$this->lastGroup]['title'] = $title;

        return $this;
    }

    public function withDescription(string $description): self
    {
        $this->permissions[$this->lastGroup]['description'] = $description;

        return $this;
    }

    public function withPermissions(array $permissions): self
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

    public function defaults(
        bool $navigations = true,
        bool $users = true,
        bool $extensions = true,
        bool $appearance = true,
        bool $preferences = true,
        bool $roles = true,
    ): void {
        if ($navigations) {
            $this
                ->withGroup('navigation')
                ->withTitle(__('Navigations'))
                ->withPermissions([
                    'create' => __('Can create navigation'),
                    'read' => __('Can view navigations'),
                    'update' => __('Can edit navigations'),
                    'delete' => __('Can delete navigations'),
                ]);
        }

        if ($users) {
            $this
                ->withGroup('user')
                ->withTitle(__('Users'))
                ->withPermissions([
                    'create' => __('Can create user'),
                    'read' => __('Can view users'),
                    'update' => __('Can edit users'),
                    'delete' => __('Can delete users'),
                ]);
        }

        if ($extensions) {
            $this
                ->withGroup('extension')
                ->withTitle(__('Extensions'))
                ->withPermissions([
                    'read' => __('Can view extensions'),
                    'activate' => __('Can activate extensions'),
                    'deactivate' => __('Can deactivate extensions'),
                ]);
        }

        if ($appearance) {
            $this
                ->withGroup('appearance:editor')
                ->withTitle(__('Theme editor'))
                ->withPermissions([
                    'read' => __('Can view theme codes'),
                    'update' => __('Can edit theme codes'),
                ]);
        }

        if ($preferences) {
            $this
                ->withGroup('preference')
                ->withTitle(__('Preferences'))
                ->withDescription(__('Select which settings can be edited'))
                ->withPermissions(
                    $this->panel
                        ->preference()
                        ->ignoreAuthorization()
                        ->toDotted(all: true)
                        ->toArray()
                );

            $this->panel->preference()->withAuthorization();
        }

        if ($roles) {
            $this
                ->withGroup('role')
                ->withTitle(__('User roles'))
                ->withPermissions([
                    'create' => __('Can create user role'),
                    'read' => __('Can view user roles'),
                    'update' => __('Can edit user roles'),
                    'delete' => __('Can delete user roles'),
                ]);
        }
    }
}
