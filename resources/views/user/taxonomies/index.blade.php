<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <x-feadmin::page.head
                :back="$taxonomy ? panel_route('taxonomies.index', ['taxonomy' => $taxonomyItem->name()]) : null">
            <x-feadmin::page.title>
                {{ $taxonomy ? __('Düzenle: :term', ['term' => $taxonomy->term->title]) : $taxonomyItem->pluralName() }}
            </x-feadmin::page.title>
        </x-feadmin::page.head>
        <div @class([
            'fd-grid fd-gap-3',
            'fd-grid-cols-5' => auth()->user()->can($taxonomyItem->abilityFor('create')),
            'fd-grid-cols-3 fd-mx-auto' => !auth()->user()->can($taxonomyItem->abilityFor('create')),
        ])>
            @can($taxonomyItem->abilityFor('create'))
                <div class="fd-col-span-2">
                    <x-feadmin::form
                            :action="$taxonomy ? panel_route('taxonomies.update', $taxonomy) : panel_route('taxonomies.store')"
                            :method="$taxonomy ? 'PUT' : 'POST'"
                            :bind="$taxonomy ?? null"
                            enctype="multipart/form-data"
                    >
                        <input type="hidden" name="taxonomy" value="{{ $taxonomyItem->name() }}">
                        <input type="hidden" name="_locale" value="{{ $locale }}">
                        <div class="fd-space-y-3">
                            <x-feadmin::card padding>
                                <x-feadmin::card.title>@lang('Yeni :taxonomy', ['taxonomy' => Str::lower($taxonomyItem->singularName())])</x-feadmin::card.title>
                                <div class="fd-space-y-3">
                                    <x-feadmin::form.group name="title" :label="__('Başlık')">
                                        <x-feadmin::form.input
                                                :default="$taxonomy?->term?->title"
                                                :translatable="$isTranslatable"
                                        />
                                    </x-feadmin::form.group>
                                    <x-feadmin::form.group name="slug" :label="__('Sabit URL')">
                                        <x-feadmin::form.input
                                                :default="$taxonomy?->term?->slug"
                                                :translatable="$isTranslatable"
                                        />
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
                                </div>
                            </x-feadmin::card>
                            <x-feadmin::tabs container="taxonomy" :default="array_keys($fieldSections)[0] ?? null">
                                <x-feadmin::tabs.header>
                                    @foreach ($fieldSections as $id => $section)
                                        <x-feadmin::tabs.button
                                                :id="$id">{{ $section['title'] }}</x-feadmin::tabs.button>
                                    @endforeach
                                </x-feadmin::tabs.header>
                                @foreach($fieldSections as $id => $section)
                                    <x-feadmin::tabs.content :for="$id">
                                        <div class="fd-space-y-3">
                                            @foreach ($section['fields'] as $field)
                                                <x-feadmin::form.field
                                                        :field="$field"
                                                        :default="$metafields[$field['key']] ?? null"
                                                />
                                            @endforeach
                                        </div>
                                    </x-feadmin::tabs.content>
                                @endforeach
                            </x-feadmin::tabs>
                            <x-feadmin::card padding>
                                <x-feadmin::button type="submit">
                                    {{ $taxonomy ? __('Güncelle') : __('Oluştur') }}
                                </x-feadmin::button>
                            </x-feadmin::card>
                        </div>
                        <x-feadmin::form.sticky-submit />
                    </x-feadmin::form>
                </div>
            @endcan
            <div class="fd-space-y-3 fd-col-span-3">
                <x-feadmin::table>
                    <x-feadmin::table.head>
                        <x-feadmin::table.th>@lang('Başlık')</x-feadmin::table.th>
                        <x-feadmin::table.th>@lang('Üst :taxonomy', ['taxonomy' => $taxonomyItem->singularName()])</x-feadmin::table.th>
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
                                    @if ($taxonomyValue->parent)
                                        @can($taxonomyItem->abilityFor('update'))
                                            <a href="{{ panel_route('taxonomies.edit', $taxonomyValue->parent) }}">{{ $taxonomyValue->parent->term->title }}</a>
                                        @else
                                            <span>{{ $taxonomyValue->parent->term->title }}</span>
                                        @endcan
                                    @else
                                        <span class="fd-text-zinc-400">-</span>
                                    @endif
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
