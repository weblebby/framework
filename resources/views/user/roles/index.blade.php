<x-weblebby::layouts.panel>
    <x-weblebby::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-weblebby::page.head :back="panel_route('users.index')">
            <x-slot:actions>
                @can('role:create')
                    <x-weblebby::button
                            as="a"
                            :href="panel_route('roles.create')"
                            icon="plus"
                            size="sm"
                    >@lang('Create role')</x-weblebby::button>
                @endcan
            </x-slot:actions>
            <x-weblebby::page.title>@lang('User roles')</x-weblebby::page.title>
            <x-weblebby::page.subtitle>@lang('Manage user roles.')</x-weblebby::page.subtitle>
        </x-weblebby::page.head>
        <div class="fd-space-y-3">
            <x-weblebby::table>
                <x-weblebby::table.head>
                    <x-weblebby::table.th>@lang('Role')</x-weblebby::table.th>
                    <x-weblebby::table.th>@lang('Created')</x-weblebby::table.th>
                    <x-weblebby::table.th />
                </x-weblebby::table.head>
                <x-weblebby::table.body>
                    @foreach ($roles as $role)
                        <tr>
                            <x-weblebby::table.td class="fd-font-medium fd-text-lg">
                                @can('role:update')
                                    <a href="{{ panel_route('roles.edit', $role) }}">{{ $role->name }}</a>
                                @else
                                    <span>{{ $role->name }}</span>
                                @endcan
                            </x-weblebby::table.td>
                            <x-weblebby::table.td>{{ Date::short($role->created_at) }}</x-weblebby::table.td>
                            <x-weblebby::table.td>
                                @unless ($role->is_default)
                                    <div class="fd-ml-auto">
                                        @can('role:delete')
                                            <x-weblebby::button
                                                    size="sm"
                                                    variant="red"
                                                    data-modal-open="#modal-delete-role"
                                                    :data-action="panel_route('roles.destroy', $role)"
                                            >@lang('Delete')</x-weblebby::button>
                                        @endcan
                                    </div>
                                @endif
                            </x-weblebby::table.td>
                        </tr>
                    @endforeach
                </x-weblebby::table.body>
            </x-weblebby::table>
            {{ $roles->links() }}
        </div>
    </x-weblebby::page>
    @can('role:delete')
        <x-weblebby::modal.destroy
                id="modal-delete-role"
                :title="__('Delete the role')"
        />
    @endcan
</x-weblebby::layouts.panel>
