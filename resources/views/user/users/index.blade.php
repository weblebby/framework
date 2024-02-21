<x-weblebby::layouts.panel>
    <x-weblebby::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-weblebby::page.head>
            <x-slot:actions>
                @can('user:create')
                    <x-weblebby::button
                            as="a"
                            :href="panel_route('users.create')"
                            icon="plus"
                            size="sm"
                    >@lang('Create user')</x-weblebby::button>
                @endcan
                @can('role:read')
                    <x-weblebby::button
                            as="a"
                            :href="panel_route('roles.index')"
                            variant="sky"
                            size="sm"
                    >@lang('Roles')</x-weblebby::button>
                @endcan
            </x-slot:actions>
            <x-weblebby::page.title>@lang('Users')</x-weblebby::page.title>
            <x-weblebby::page.subtitle>@lang('Manage users.')</x-weblebby::page.subtitle>
        </x-weblebby::page.head>
        <div class="fd-space-y-3">
            <x-weblebby::table>
                <x-weblebby::table.head>
                    <x-weblebby::table.th>@lang('Fullname')</x-weblebby::table.th>
                    <x-weblebby::table.th>@lang('Created')</x-weblebby::table.th>
                    <x-weblebby::table.th />
                </x-weblebby::table.head>
                <x-weblebby::table.body>
                    @foreach ($users as $user)
                        <tr>
                            <x-weblebby::table.td class="fd-font-medium fd-text-lg">
                                @can('user:update')
                                    <a href="{{ panel_route('users.edit', $user) }}">{{ $user->name }}</a>
                                @else
                                    <span>{{ $user->name }}</span>
                                @endcan
                            </x-weblebby::table.td>
                            <x-weblebby::table.td>{{ Date::short($user->created_at) }}</x-weblebby::table.td>
                            <x-weblebby::table.td>
                                <div class="fd-ml-auto">
                                    @can('user:delete')
                                        <x-weblebby::button
                                                size="sm"
                                                variant="red"
                                                data-modal-open="#modal-delete-user"
                                                :data-action="panel_route('users.destroy', $user)"
                                        >@lang('Delete')</x-weblebby::button>
                                    @endcan
                                </div>
                            </x-weblebby::table.td>
                        </tr>
                    @endforeach
                </x-weblebby::table.body>
            </x-weblebby::table>
            {{ $users->links() }}
        </div>
    </x-weblebby::page>
    @can('user:delete')
        <x-weblebby::modal.destroy
                id="modal-delete-user"
                :title="__('Delete the user')"
        />
    @endcan
</x-weblebby::layouts.panel>
