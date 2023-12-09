<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <x-feadmin::page.head
                :back="$taxonomy ? panel_route('taxonomies.index', ['taxonomy' => $taxonomyItem->name()]) : null">
            <x-feadmin::page.title>{{ $taxonomyItem->pluralName() }}</x-feadmin::page.title>
        </x-feadmin::page.head>
        <div @class([
            'fd-grid fd-gap-3',
            'fd-grid-cols-5' => auth()->user()->can($taxonomyItem->abilityFor('create')),
            'fd-grid-cols-3 fd-mx-auto' => !auth()->user()->can($taxonomyItem->abilityFor('create')),
        ])>
            @can($taxonomyItem->abilityFor('create'))
                <div class="fd-col-span-2">
                    <x-feadmin::card padding>
                        <x-feadmin::card.title>@lang('Yeni :taxonomy', ['taxonomy' => Str::lower($taxonomyItem->singularName())])</x-feadmin::card.title>
                        <x-feadmin::form
                                :action="$taxonomy ? panel_route('taxonomies.update', $taxonomy) : panel_route('taxonomies.store')"
                                :method="$taxonomy ? 'PUT' : 'POST'"
                                :bind=" $taxonomy ?? null
                        ">
                            <input type="hidden" name="taxonomy" value="{{ $taxonomyItem->name() }}">
                            <div class="fd-space-y-3">
                                <x-feadmin::form.group name="title" :label="__('Başlık')">
                                    <x-feadmin::form.input :default="$taxonomy?->term?->title" />
                                </x-feadmin::form.group>
                                <x-feadmin::form.group name="description" :label="__('Açıklama')">
                                    <x-feadmin::form.textarea data-ckeditor />
                                </x-feadmin::form.group>
                                <x-feadmin::form.group name="parent_id"
                                                       :label="__('Üst :taxonomy', ['taxonomy' => Str::lower($taxonomyItem->singularName())])">
                                    <x-feadmin::form.select>
                                        <option value="">@lang('Yok')</option>
                                        @foreach ($taxonomiesForParentSelect as $taxonomyValue)
                                            <x-feadmin::form.option
                                                    value="{{ $taxonomyValue->id }}">{{ $taxonomyValue->term->title }}</x-feadmin::form.option>
                                        @endforeach
                                    </x-feadmin::form.select>
                                </x-feadmin::form.group>
                                <x-feadmin::button type="submit">
                                    {{ $taxonomy ? __('Güncelle') : __('Oluştur') }}
                                </x-feadmin::button>
                            </div>
                        </x-feadmin::form>
                    </x-feadmin::card>
                </div>
            @endcan
            <div class="fd-space-y-3 fd-col-span-3">
                <x-feadmin::table>
                    <x-feadmin::table.head>
                        <x-feadmin::table.th>@lang('Başlık')</x-feadmin::table.th>
                        <x-feadmin::table.th>@lang('Değişiklik tarihi')</x-feadmin::table.th>
                        <x-feadmin::table.th />
                    </x-feadmin::table.head>
                    <x-feadmin::table.body>
                        @foreach ($taxonomies as $taxonomyValue)
                            <tr>
                                <x-feadmin::table.td class="fd-font-medium fd-text-lg">
                                    @can($taxonomyItem->abilityFor('update'))
                                        <a href="{{ panel_route('taxonomies.edit', $taxonomyValue) }}">{{ $taxonomyValue->term->title }}</a>
                                    @else
                                        <span>{{ $taxonomyValue->term->title }}</span>
                                    @endcan
                                </x-feadmin::table.td>
                                <x-feadmin::table.td>
                                    {{ Date::short($taxonomyValue->updated_at) }}
                                </x-feadmin::table.td>
                                <x-feadmin::table.td>
                                    <div class="fd-ml-auto">
                                        @can($taxonomyItem->abilityFor('delete'))
                                            <x-feadmin::button
                                                    size="sm"
                                                    variant="red"
                                                    data-modal-open="#modal-delete-taxonomy"
                                                    :data-action="panel_route('taxonomies.destroy', $taxonomyValue)"
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
        </div>
    </x-feadmin::page>
    @can($taxonomyItem->abilityFor('delete'))
        <x-feadmin::modal.destroy
                id="modal-delete-taxonomy"
                :title="__(':name siliyorsunuz', ['name' => $taxonomyItem->singularName()])"
        />
    @endcan
</x-feadmin::layouts.panel>
