<x-feadmin::layouts.panel>
    <x-feadmin::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-feadmin::page.head>
            <x-slot:actions>
                @can('user:create')
                    <x-feadmin::button
                            as="a"
                            :href="panel_route('users.create')"
                            icon="plus"
                            size="sm"
                    >@lang('Yeni kullanıcı')</x-feadmin::button>
                @endcan
                @can('role:read')
                    <x-feadmin::button
                            as="a"
                            :href="panel_route('roles.index')"
                            variant="sky"
                            size="sm"
                    >@lang('Roller')</x-feadmin::button>
                @endcan
            </x-slot:actions>
            <x-feadmin::page.title>@lang('Kullanıcılar')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang('Kullanıcıları yönetin')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <div class="fd-space-y-3">
            <x-feadmin::table>
                <x-feadmin::table.head>
                    <x-feadmin::table.th>@lang('İsim')</x-feadmin::table.th>
                    <x-feadmin::table.th>@lang('Oluşturulma tarihi')</x-feadmin::table.th>
                    <x-feadmin::table.th />
                </x-feadmin::table.head>
                <x-feadmin::table.body>
                    @foreach ($users as $user)
                        <tr>
                            <x-feadmin::table.td class="fd-font-medium fd-text-lg">
                                @can('user:update')
                                    <a href="{{ panel_route('users.edit', $user) }}">{{ $user->name }}</a>
                                @else
                                    <span>{{ $user->name }}</span>
                                @endcan
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>{{ Date::short($user->created_at) }}</x-feadmin::table.td>
                            <x-feadmin::table.td>
                                <div class="fd-ml-auto">
                                    @can('user:delete')
                                        <x-feadmin::button
                                                size="sm"
                                                variant="red"
                                                data-modal-open="#modal-delete-user"
                                                :data-action="panel_route('users.destroy', $user)"
                                        >@lang('Sil')</x-feadmin::button>
                                    @endcan
                                </div>
                            </x-feadmin::table.td>
                        </tr>
                    @endforeach
                </x-feadmin::table.body>
            </x-feadmin::table>
            {{ $users->links() }}
        </div>
    </x-feadmin::page>
    @can('user:delete')
        <x-feadmin::modal.destroy
                id="modal-delete-user"
                :title="__('Kullanıcyı sil')"
        />
    @endcan
</x-feadmin::layouts.panel>
