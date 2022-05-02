<?php

namespace Feadmin\Hooks;

class Permission
{
    private Panel $panel;

    private string $lastGroup;

    private array $permissions = [];

    public function __construct(Panel $panel)
    {
        $this->panel = $panel;
    }

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

    public function defaults(
        bool $preferences = true,
        bool $locales = true,
        bool $users = true,
        bool $roles = true,
        bool $extensions = true,
        bool $navigations = true,
    ): void {
        if ($locales) {
            $this->group('locale')
                ->title(__('Diller'))
                ->permissions([
                    'create' => __('Dil oluşturabilir'),
                    'read' => __('Dilleri görüntüleyebilir'),
                    'update' => __('Dilleri düzenleyebilir'),
                    'delete' => __('Dilleri silebilir'),
                    'translate' => __('Çevirileri düzenleyebilir'),
                ]);
        }

        if ($users) {
            $this->group('user')
                ->title(__('Kullanıcılar'))
                ->permissions([
                    'create' => __('Kullanıcı oluşturabilir'),
                    'read' => __('Kullanıcıları görüntüleyebilir'),
                    'update' => __('Kullanıcıları düzenleyebilir'),
                    'delete' => __('Kullanıcıları silebilir'),
                ]);
        }

        if ($preferences) {
            $this->group('preference')
                ->title(__('Tercihler'))
                ->description(__('Hangi ayarları düzenleyebileceğini seçin'))
                ->permissions(
                    $this->panel
                        ->preference()
                        ->ignoreAuthorization()
                        ->toDottedAll()
                        ->toArray()
                );

            $this->panel->preference()->checkAuthorization();
        }

        if ($roles) {
            $this->group('role')
                ->title(__('Kullanıcı rolleri'))
                ->permissions([
                    'create' => __('Kullanıcı rolü oluşturabilir'),
                    'read' => __('Kullanıcı rollerini görüntüleyebilir'),
                    'update' => __('Kullanıcı rollerini düzenleyebilir'),
                    'delete' => __('Kullanıcı rollerini silebilir'),
                ]);
        }

        if ($extensions) {
            $this->group('extension')
                ->title(__('Eklentiler'))
                ->permissions([
                    'read' => __('Eklentileri görüntüleyebilir'),
                    'update' => __('Eklentileri düzenleyebilir'),
                    'delete' => __('Eklentileri silebilir'),
                ]);
        }

        if ($navigations) {
            $this->group('navigation')
                ->title(__('Navigasyonlar'))
                ->permissions([
                    'create' => __('Navigasyon oluşturabilir'),
                    'read' => __('Navigasyonları görüntüleyebilir'),
                    'update' => __('Navigasyonları düzenleyebilir'),
                    'delete' => __('Navigasyonları silebilir'),
                ]);
        }
    }
}
