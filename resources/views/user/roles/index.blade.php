<x-feadmin::layouts.panel>
    <x-feadmin::page class="w-2/3 mx-auto">
        <x-feadmin::page.head :back="route('admin::users.index')">
            <x-slot name="actions">
                @can('role:create')
                    <x-feadmin::button
                        as="a"
                        :href="route('admin::roles.create')"
                        icon="plus"
                        size="sm"
                    >@t('Yeni rol', 'panel')</x-feadmin::button>
                @endcan
            </x-slot>
            <x-feadmin::page.title>@t('Kullanıcı rolleri', 'panel')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Rolleri yönetin', 'panel')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <div class="space-y-3">
            <x-feadmin::table>
                <x-feadmin::table.head>
                    <x-feadmin::table.th>@t('Rol', 'panel')</x-feadmin::table.th>
                    <x-feadmin::table.th>@t('Oluşturulma tarihi', 'panel')</x-feadmin::table.th>
                    <x-feadmin::table.th />
                </x-feadmin::table.head>
                <x-feadmin::table.body>
                    @foreach ($roles as $role)
                        <tr>
                            <x-feadmin::table.td class="font-medium text-lg">
                                @can('role:update')
                                    <a href="{{ route('admin::roles.edit', $role) }}">{{ $role->name }}</a>
                                @else
                                    <span>{{ $role->name }}</span>
                                @endcan
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>{{ Localization::date($role->created_at) }}</x-feadmin::table.td>
                            <x-feadmin::table.td>
                                @unless ($role->is_default)
                                    <div class="ml-auto">
                                        @can('role:delete')
                                            <x-feadmin::button
                                                size="sm"
                                                variant="red"
                                                data-modal-open="#modal-delete-role"
                                                :data-action="route('admin::roles.destroy', $role)"
                                            >@t('Sil', 'panel')</x-feadmin::button>
                                        @endcan
                                    </div>
                                @endif
                            </x-feadmin::table.td>
                        </tr>
                    @endforeach
                </x-feadmin::table.body>
            </x-feadmin::table>
            {{ $roles->links() }}
        </div>
    </x-feadmin::page>
    @can('role:delete')
        <x-feadmin::modal.destroy
            id="modal-delete-role"
            :title="t('Rolü sil', 'panel')"
        />
    @endcan
</x-feadmin::layouts.panel>