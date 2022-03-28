<x-feadmin::layouts.panel>
    <x-feadmin::page class="w-2/3 mx-auto">
        <x-feadmin::page.head>
            <x-slot name="actions">
                @can('user:create')
                    <x-feadmin::button
                        as="a"
                        :href="route('admin::users.create')"
                        icon="plus"
                        size="sm"
                    >@t('Yeni kullanıcı', 'panel')</x-feadmin::button>
                @endcan
                @can('role:read')
                    <x-feadmin::button
                        as="a"
                        :href="route('admin::roles.index')"
                        variant="sky"
                        size="sm"
                    >@t('Roller', 'panel')</x-feadmin::button>
                @endcan
            </x-slot>
            <x-feadmin::page.title>@t('Kullanıcılar', 'panel')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Kullanıcıları yönetin', 'panel')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <div class="space-y-3">
            <x-feadmin::table>
                <x-feadmin::table.head>
                    <x-feadmin::table.th>@t('İsim', 'panel')</x-feadmin::table.th>
                    <x-feadmin::table.th>@t('Oluşturulma tarihi', 'panel')</x-feadmin::table.th>
                    <x-feadmin::table.th />
                </x-feadmin::table.head>
                <x-feadmin::table.body>
                    @foreach ($users as $user)
                        <tr>
                            <x-feadmin::table.td class="font-medium text-lg">
                                @can('user:update')
                                    <a href="{{ route('admin::users.edit', $user) }}">{{ $user->full_name }}</a>
                                @else
                                    <span>{{ $user->full_name }}</span>
                                @endcan
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>{{ Localization::date($user->created_at) }}</x-feadmin::table.td>
                            <x-feadmin::table.td>
                                <div class="ml-auto">
                                    @can('user:delete')
                                        <x-feadmin::button
                                            size="sm"
                                            variant="red"
                                            data-modal-open="#modal-delete-user"
                                            :data-action="route('admin::users.destroy', $user)"
                                        >@t('Sil', 'panel')</x-feadmin::button>
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
            :title="t('Kullanıcyı sil', 'panel')"
        />
    @endcan
</x-feadmin::layouts.panel>