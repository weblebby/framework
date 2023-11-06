<x-feadmin::layouts.panel>
    <x-feadmin::page class="fd-w-2/3 fd-mx-auto">
        <x-feadmin::page.head>
            <x-slot name="actions">
                @can('post:create')
                    <x-feadmin::button
                            as="a"
                            :href="panel_route('posts.create')"
                            icon="plus"
                            size="sm"
                    >
                        @lang('Yeni yazı')
                    </x-feadmin::button>
                @endcan
            </x-slot>
            <x-feadmin::page.title>@lang('Yazılar')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang('Yazılarınızı yönetin')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <div class="fd-space-y-3">
            <x-feadmin::table>
                <x-feadmin::table.head>
                    <x-feadmin::table.th>@lang('Başlık')</x-feadmin::table.th>
                    <x-feadmin::table.th>@lang('Yayında')</x-feadmin::table.th>
                    <x-feadmin::table.th>@lang('Değişiklik tarihi')</x-feadmin::table.th>
                    <x-feadmin::table.th />
                </x-feadmin::table.head>
                <x-feadmin::table.body>
                    @foreach ($posts as $post)
                        <tr>
                            <x-feadmin::table.td class="fd-font-medium fd-text-lg">
                                @can('post:update')
                                    <a href="{{ panel_route('posts.edit', $post) }}">{{ $post->title }}</a>
                                @else
                                    <span>{{ $post->title }}</span>
                                @endcan
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                {{ $post->published_at?->isPast() ? __('Evet') : __('Hayır') }}
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                {{ Date::short($post->updated_at) }}
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                <div class="fd-ml-auto">
                                    @can('post:delete')
                                        <x-feadmin::button
                                                size="sm"
                                                variant="red"
                                                data-modal-open="#modal-delete-post"
                                                :data-action="panel_route('posts.destroy', $post)"
                                        >
                                            @lang('Sil')
                                        </x-feadmin::button>
                                    @endcan
                                </div>
                            </x-feadmin::table.td>
                        </tr>
                    @endforeach
                </x-feadmin::table.body>
            </x-feadmin::table>
            {{ $posts->links() }}
        </div>
    </x-feadmin::page>
    @can('post:delete')
        <x-feadmin::modal.destroy id="modal-delete-post" :title="__('Yazıyı sil')" />
    @endcan
</x-feadmin::layouts.panel>
