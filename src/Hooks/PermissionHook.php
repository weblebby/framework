<?php

namespace Feadmin\Hooks;

use Feadmin\Items\PanelItem;

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
                    ->map(fn($_, $permKey) => "{$groupKey}:{$permKey}")
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
    ): void
    {
        if ($navigations) {
            $this
                ->withGroup('navigation')
                ->withTitle(__('Navigasyonlar'))
                ->withPermissions([
                    'create' => __('Navigasyon oluşturabilir'),
                    'read' => __('Navigasyonları görüntüleyebilir'),
                    'update' => __('Navigasyonları düzenleyebilir'),
                    'delete' => __('Navigasyonları silebilir'),
                ]);
        }

        if ($users) {
            $this
                ->withGroup('user')
                ->withTitle(__('Kullanıcılar'))
                ->withPermissions([
                    'create' => __('Kullanıcı oluşturabilir'),
                    'read' => __('Kullanıcıları görüntüleyebilir'),
                    'update' => __('Kullanıcıları düzenleyebilir'),
                    'delete' => __('Kullanıcıları silebilir'),
                ]);
        }

        if ($extensions) {
            $this
                ->withGroup('extension')
                ->withTitle(__('Eklentiler'))
                ->withPermissions([
                    'read' => __('Eklentileri görüntüleyebilir'),
                    'update' => __('Eklentileri düzenleyebilir'),
                    'delete' => __('Eklentileri silebilir'),
                ]);
        }

        if ($appearance) {
            $this
                ->withGroup('appearance:editor')
                ->withTitle(__('Tema editörü'))
                ->withPermissions([
                    'read' => __('Tema kodlarını görüntüleyebilir'),
                    'update' => __('Tema kodlarını düzenleyebilir'),
                ]);
        }

        if ($preferences) {
            $this
                ->withGroup('preference')
                ->withTitle(__('Tercihler'))
                ->withDescription(__('Hangi ayarları düzenleyebileceğini seçin'))
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
                ->withTitle(__('Kullanıcı rolleri'))
                ->withPermissions([
                    'create' => __('Kullanıcı rolü oluşturabilir'),
                    'read' => __('Kullanıcı rollerini görüntüleyebilir'),
                    'update' => __('Kullanıcı rollerini düzenleyebilir'),
                    'delete' => __('Kullanıcı rollerini silebilir'),
                ]);
        }
    }
}
