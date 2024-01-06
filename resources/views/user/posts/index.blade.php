<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <x-feadmin::page.head>
            <x-slot:actions>
                @can($postable::getPostAbilityFor('create'))
                    <x-feadmin::button
                            as="a"
                            :href="panel_route('posts.create', ['type' => $postable::getModelName()])"
                            icon="plus"
                            size="sm"
                    >
                        @lang('Yeni :name', ['name' => Str::lower($postable::getSingularName())])
                    </x-feadmin::button>
                @endcan
            </x-slot:actions>
            <x-feadmin::page.title>{{ $postable::getPluralName() }}</x-feadmin::page.title>
        </x-feadmin::page.head>
        <div class="fd-space-y-3">
            <form class="fd-flex fd-items-center fd-gap-2" method="GET">
                <input type="hidden" name="type" value="{{ $postable::getModelName() }}">
                <x-feadmin::form.group name="term" class="fd-flex-[3]">
                    <x-feadmin::form.input type="search" :placeholder="__('Ara')" />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="status" class="fd-flex-1">
                    <x-feadmin::form.select onchange="this.form.submit()">
                        <x-feadmin::form.option value="">@lang('Tümü')</x-feadmin::form.option>
                        @foreach(\Feadmin\Enums\PostStatusEnum::cases() as $status)
                            <x-feadmin::form.option :value="$status->value">
                                {{ $status->label() }}
                            </x-feadmin::form.option>
                        @endforeach
                    </x-feadmin::form.select>
                </x-feadmin::form.group>
            </form>
            <x-feadmin::table>
                <x-feadmin::table.head>
                    <x-feadmin::table.th>@lang('Başlık')</x-feadmin::table.th>
                    <x-feadmin::table.th>@lang('Kategoriler')</x-feadmin::table.th>
                    <x-feadmin::table.th>@lang('Etiketler')</x-feadmin::table.th>
                    <x-feadmin::table.th>@lang('Yayında')</x-feadmin::table.th>
                    <x-feadmin::table.th>@lang('Değişiklik tarihi')</x-feadmin::table.th>
                    <x-feadmin::table.th />
                </x-feadmin::table.head>
                <x-feadmin::table.body>
                    @foreach ($posts as $post)
                        <tr>
                            <x-feadmin::table.td class="fd-font-medium fd-text-lg">
                                @can($postable::getPostAbilityFor('update'))
                                    <a href="{{ panel_route('posts.edit', $post) }}">{{ $post->title }}</a>
                                @else
                                    <span>{{ $post->title }}</span>
                                @endcan
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                {{ Str::limit($post->getTaxonomiesFor('category')->implode('term.title', ', '), 30) ?: '-' }}
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                {{ Str::limit($post->getTaxonomiesFor('tag')->implode('term.title', ', '), 30) ?: '-' }}
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                {{ $post->published_at?->isPast() ? __('Evet') : __('Hayır') }}
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                {{ Date::short($post->updated_at) }}
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                <div class="fd-flex fd-items-center fd-gap-2 fd-ml-auto">
                                    @can($postable::getPostAbilityFor('update'))
                                        <x-feadmin::button
                                                as="a"
                                                variant="light"
                                                :href="panel_route('posts.edit', $post)"
                                                icon="pencil-fill"
                                        />
                                    @endcan
                                    @can($postable::getPostAbilityFor('delete'))
                                        <x-feadmin::button
                                                variant="red"
                                                icon="trash"
                                                data-modal-open="#modal-delete-post"
                                                :data-action="panel_route('posts.destroy', $post)"
                                        />
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
    @can($postable::getPostAbilityFor('delete'))
        <x-feadmin::modal.destroy id="modal-delete-post"
                                  :title="__(':name siliyorsunuz', ['name' => $postable::getSingularName()])" />
    @endcan
</x-feadmin::layouts.panel>
