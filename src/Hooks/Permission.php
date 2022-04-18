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
                ->title(t('Diller', 'admin'))
                ->permissions([
                    'create' => t('Dil oluşturabilir', 'admin'),
                    'read' => t('Dilleri görüntüleyebilir', 'admin'),
                    'update' => t('Dilleri düzenleyebilir', 'admin'),
                    'delete' => t('Dilleri silebilir', 'admin'),
                    'translate' => t('Çevirileri düzenleyebilir', 'admin'),
                ]);
        }

        if ($users) {
            $this->group('user')
                ->title(t('Kullanıcılar', 'admin'))
                ->permissions([
                    'create' => t('Kullanıcı oluşturabilir', 'admin'),
                    'read' => t('Kullanıcıları görüntüleyebilir', 'admin'),
                    'update' => t('Kullanıcıları düzenleyebilir', 'admin'),
                    'delete' => t('Kullanıcıları silebilir', 'admin'),
                ]);
        }

        if ($preferences) {
            $this->group('preference')
                ->title(t('Tercihler', 'admin'))
                ->description(t('Hangi ayarları düzenleyebileceğini seçin', 'admin'))
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
                ->title(t('Kullanıcı rolleri', 'admin'))
                ->permissions([
                    'create' => t('Kullanıcı rolü oluşturabilir', 'admin'),
                    'read' => t('Kullanıcı rollerini görüntüleyebilir', 'admin'),
                    'update' => t('Kullanıcı rollerini düzenleyebilir', 'admin'),
                    'delete' => t('Kullanıcı rollerini silebilir', 'admin'),
                ]);
        }

        if ($extensions) {
            $this->group('extension')
                ->title(t('Eklentiler', 'admin'))
                ->permissions([
                    'read' => t('Eklentileri görüntüleyebilir', 'admin'),
                    'update' => t('Eklentileri düzenleyebilir', 'admin'),
                    'delete' => t('Eklentileri silebilir', 'admin'),
                ]);
        }

        if ($navigations) {
            $this->group('navigation')
                ->title(t('Navigasyonlar', 'admin'))
                ->permissions([
                    'create' => t('Navigasyon oluşturabilir', 'admin'),
                    'read' => t('Navigasyonları görüntüleyebilir', 'admin'),
                    'update' => t('Navigasyonları düzenleyebilir', 'admin'),
                    'delete' => t('Navigasyonları silebilir', 'admin'),
                ]);
        }
    }
}
