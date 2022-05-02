<x-feadmin::layouts.panel>
    <x-feadmin::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-feadmin::page.head :back="panel_route('users.index')">
            <x-slot name="actions">
                @can('role:create')
                    <x-feadmin::button
                        as="a"
                        :href="panel_route('roles.create')"
                        icon="plus"
                        size="sm"
                    >@lang('Yeni rol')</x-feadmin::button>
                @endcan
            </x-slot>
            <x-feadmin::page.title>@lang('Kullanıcı rolleri')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang('Rolleri yönetin')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <div class="fd-space-y-3">
            <x-feadmin::table>
                <x-feadmin::table.head>
                    <x-feadmin::table.th>@lang('Rol')</x-feadmin::table.th>
                    <x-feadmin::table.th>@lang('Oluşturulma tarihi')</x-feadmin::table.th>
                    <x-feadmin::table.th />
                </x-feadmin::table.head>
                <x-feadmin::table.body>
                    @foreach ($roles as $role)
                        <tr>
                            <x-feadmin::table.td class="fd-font-medium fd-text-lg">
                                @can('role:update')
                                    <a href="{{ panel_route('roles.edit', $role) }}">{{ $role->name }}</a>
                                @else
                                    <span>{{ $role->name }}</span>
                                @endcan
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>{{ Localization::date($role->created_at) }}</x-feadmin::table.td>
                            <x-feadmin::table.td>
                                @unless ($role->is_default)
                                    <div class="fd-ml-auto">
                                        @can('role:delete')
                                            <x-feadmin::button
                                                size="sm"
                                                variant="red"
                                                data-modal-open="#modal-delete-role"
                                                :data-action="panel_route('roles.destroy', $role)"
                                            >@lang('Sil')</x-feadmin::button>
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
            :title="__('Rolü sil')"
        />
    @endcan
</x-feadmin::layouts.panel>