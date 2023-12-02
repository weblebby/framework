<x-feadmin::layouts.panel>
    <x-feadmin::page class="fd-w-2/3 fd-mx-auto">
        <x-feadmin::page.head>
            <x-slot name="actions">
                @can($taxonomyOption->abilityFor('create'))
                    <x-feadmin::button
                            as="a"
                            :href="panel_route('taxonomies.create', ['taxonomy' => $taxonomyOption->name()])"
                            icon="plus"
                            size="sm"
                    >
                        @lang('Yeni :name', ['name' => Str::lower($taxonomyOption->singularName())])
                    </x-feadmin::button>
                @endcan
            </x-slot>
            <x-feadmin::page.title>{{ $taxonomyOption->pluralName() }}</x-feadmin::page.title>
        </x-feadmin::page.head>
        <div class="fd-space-y-3">
            <x-feadmin::table>
                <x-feadmin::table.head>
                    <x-feadmin::table.th>@lang('Başlık')</x-feadmin::table.th>
                    <x-feadmin::table.th>@lang('Değişiklik tarihi')</x-feadmin::table.th>
                    <x-feadmin::table.th />
                </x-feadmin::table.head>
                <x-feadmin::table.body>
                    @foreach ($taxonomies as $taxonomy)
                        <tr>
                            <x-feadmin::table.td class="fd-font-medium fd-text-lg">
                                @can($taxonomyOption->abilityFor('update'))
                                    <a href="{{ panel_route('taxonomies.edit', $taxonomy) }}">{{ $taxonomy->term->title }}</a>
                                @else
                                    <span>{{ $taxonomy->term->title }}</span>
                                @endcan
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                {{ Date::short($taxonomy->updated_at) }}
                            </x-feadmin::table.td>
                            <x-feadmin::table.td>
                                <div class="fd-ml-auto">
                                    @can($taxonomyOption->abilityFor('delete'))
                                        <x-feadmin::button
                                                size="sm"
                                                variant="red"
                                                data-modal-open="#modal-delete-taxonomy"
                                                :data-action="panel_route('taxonomies.destroy', $taxonomy)"
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
            {{ $taxonomies->links() }}
        </div>
    </x-feadmin::page>
    @can($taxonomyOption->abilityFor('delete'))
        <x-feadmin::modal.destroy
                id="modal-delete-taxonomy"
                :title="__(':name siliyorsunuz', ['name' => $taxonomyOption->singularName()])"
        />
    @endcan
</x-feadmin::layouts.panel>
