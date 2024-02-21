<x-weblebby::layouts.panel>
    <x-weblebby::page>
        <x-weblebby::page.head>
            <x-slot:actions>
                @can($postable::getPostAbilityFor('create'))
                    <x-weblebby::button
                            as="a"
                            :href="panel_route('posts.create', ['type' => $postable::getModelName()])"
                            icon="plus"
                            size="sm"
                    >
                        @lang('Create :name', ['name' => Str::lower($postable::getSingularName())])
                    </x-weblebby::button>
                @endcan
            </x-slot:actions>
            <x-weblebby::page.title>{{ $postable::getPluralName() }}</x-weblebby::page.title>
        </x-weblebby::page.head>
        <div class="fd-space-y-3">
            <form class="fd-flex fd-items-center fd-gap-2" method="GET">
                <input type="hidden" name="type" value="{{ $postable::getModelName() }}">
                <x-weblebby::form.group name="term" class="fd-flex-[3]">
                    <x-weblebby::form.input type="search" :placeholder="__('Search')" />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="status" class="fd-flex-1">
                    <x-weblebby::form.select onchange="this.form.submit()">
                        <x-weblebby::form.option value="">@lang('All')</x-weblebby::form.option>
                        @foreach(\Weblebby\Framework\Enums\PostStatusEnum::cases() as $status)
                            <x-weblebby::form.option :value="$status->value">
                                {{ $status->label() }}
                            </x-weblebby::form.option>
                        @endforeach
                    </x-weblebby::form.select>
                </x-weblebby::form.group>
            </form>
            <x-weblebby::table>
                <x-weblebby::table.head>
                    <x-weblebby::table.th>@lang('Title')</x-weblebby::table.th>
                    <x-weblebby::table.th>@lang('Categories')</x-weblebby::table.th>
                    <x-weblebby::table.th>@lang('Tags')</x-weblebby::table.th>
                    <x-weblebby::table.th>@lang('Status')</x-weblebby::table.th>
                    <x-weblebby::table.th>@lang('Amendment date')</x-weblebby::table.th>
                    <x-weblebby::table.th />
                </x-weblebby::table.head>
                <x-weblebby::table.body>
                    @foreach ($posts as $post)
                        <tr>
                            <x-weblebby::table.td class="fd-font-medium fd-text-lg">
                                @can($postable::getPostAbilityFor('update'))
                                    <a href="{{ panel_route('posts.edit', $post) }}">{{ $post->title }}</a>
                                @else
                                    <span>{{ $post->title }}</span>
                                @endcan
                            </x-weblebby::table.td>
                            <x-weblebby::table.td>
                                {{ Str::limit($post->getTaxonomiesFor('category')->implode('term.title', ', '), 30) ?: '-' }}
                            </x-weblebby::table.td>
                            <x-weblebby::table.td>
                                {{ Str::limit($post->getTaxonomiesFor('tag')->implode('term.title', ', '), 30) ?: '-' }}
                            </x-weblebby::table.td>
                            <x-weblebby::table.td>
                                {{ $post->status->label() }}
                            </x-weblebby::table.td>
                            <x-weblebby::table.td>
                                {{ Date::short($post->updated_at) }}
                            </x-weblebby::table.td>
                            <x-weblebby::table.td>
                                <div class="fd-flex fd-items-center fd-gap-2 fd-ml-auto">
                                    @can($postable::getPostAbilityFor('update'))
                                        <x-weblebby::button
                                                as="a"
                                                variant="light"
                                                :href="panel_route('posts.edit', $post)"
                                                icon="pencil-fill"
                                        />
                                    @endcan
                                    @can($postable::getPostAbilityFor('delete'))
                                        <x-weblebby::button
                                                variant="red"
                                                icon="trash"
                                                data-modal-open="#modal-delete-post"
                                                :data-action="panel_route('posts.destroy', $post)"
                                        />
                                    @endcan
                                </div>
                            </x-weblebby::table.td>
                        </tr>
                    @endforeach
                </x-weblebby::table.body>
            </x-weblebby::table>
            {{ $posts->links() }}
        </div>
    </x-weblebby::page>
    @can($postable::getPostAbilityFor('delete'))
        <x-weblebby::modal.destroy
                id="modal-delete-post"
                :title="__('Deleting the :title', ['title' => $postable::getSingularName()])"
        />
    @endcan
</x-weblebby::layouts.panel>
