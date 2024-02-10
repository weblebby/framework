<x-weblebby::layouts.panel>
    <x-weblebby::page>
        <x-weblebby::page.head
                :back="$taxonomy ? panel_route('taxonomies.index', ['taxonomy' => $taxonomyItem->name()]) : null">
            <x-weblebby::page.title>
                {{ $taxonomy ? __('Düzenle: :term', ['term' => $taxonomy->term->title]) : $taxonomyItem->pluralName() }}
            </x-weblebby::page.title>
        </x-weblebby::page.head>
        <div @class([
            'fd-grid fd-gap-3',
            'fd-grid-cols-5' => auth()->user()->can($taxonomyItem->abilityFor('create')),
            'fd-grid-cols-3 fd-mx-auto' => !auth()->user()->can($taxonomyItem->abilityFor('create')),
        ])>
            @can($taxonomyItem->abilityFor('create'))
                <div class="fd-col-span-2">
                    <x-weblebby::form
                            :action="$taxonomy ? panel_route('taxonomies.update', $taxonomy) : panel_route('taxonomies.store')"
                            :method="$taxonomy ? 'PUT' : 'POST'"
                            :bind="$taxonomy ?? null"
                            enctype="multipart/form-data"
                    >
                        <input type="hidden" name="taxonomy" value="{{ $taxonomyItem->name() }}">
                        <input type="hidden" name="_locale" value="{{ $locale }}">
                        <div class="fd-space-y-3">
                            <x-weblebby::card padding>
                                @if ($taxonomy)
                                    <div class="fd-flex fd-items-center fd-justify-between fd-mb-3">
                                        <x-weblebby::card.title class="!fd-mb-0">
                                            @lang('Güncelle: :term', ['term' => $taxonomy->term->title])
                                        </x-weblebby::card.title>
                                        <x-weblebby::button
                                                as="a"
                                                :href="panel_route('taxonomies.index', ['taxonomy' => $taxonomyItem->name(), 'locale' => request('locale')])"
                                                size="sm"
                                                variant="red"
                                                icon="x"
                                                class="fd-shrink-0"
                                        />
                                    </div>
                                @else
                                    <x-weblebby::card.title>@lang('Yeni :taxonomy', ['taxonomy' => Str::lower($taxonomyItem->singularName())])</x-weblebby::card.title>
                                @endif
                                <div class="fd-space-y-3">
                                    <x-weblebby::form.group name="title" :label="__('Başlık')">
                                        <x-weblebby::form.input
                                                :default="$taxonomy?->term?->title"
                                                :translatable="$isTranslatable"
                                        />
                                    </x-weblebby::form.group>
                                    <x-weblebby::form.group name="slug" :label="__('Sabit URL')">
                                        <x-weblebby::form.input
                                                :default="$taxonomy?->term?->slug"
                                                :translatable="$isTranslatable"
                                        />
                                    </x-weblebby::form.group>
                                    <x-weblebby::form.group name="parent_id"
                                                            :label="__('Üst :taxonomy', ['taxonomy' => Str::lower($taxonomyItem->singularName())])">
                                        <x-weblebby::form.select>
                                            <option value="">@lang('Yok')</option>
                                            @foreach ($taxonomiesForParentSelect as $taxonomyValue)
                                                <x-weblebby::form.option
                                                        value="{{ $taxonomyValue->id }}">{{ $taxonomyValue->term->title }}</x-weblebby::form.option>
                                            @endforeach
                                        </x-weblebby::form.select>
                                    </x-weblebby::form.group>
                                </div>
                            </x-weblebby::card>
                            <x-weblebby::tabs container="taxonomy" :default="array_keys($fieldSections)[0] ?? null">
                                <x-weblebby::tabs.header>
                                    @foreach ($fieldSections as $id => $section)
                                        <x-weblebby::tabs.button
                                                :id="$id">{{ $section['title'] }}</x-weblebby::tabs.button>
                                    @endforeach
                                </x-weblebby::tabs.header>
                                @foreach($fieldSections as $id => $section)
                                    <x-weblebby::tabs.content :for="$id">
                                        <div class="fd-space-y-3">
                                            @foreach ($section['fields'] as $field)
                                                <x-weblebby::form.field
                                                        :field="$field"
                                                        :default="$metafields[$field['key']] ?? null"
                                                />
                                            @endforeach
                                        </div>
                                    </x-weblebby::tabs.content>
                                @endforeach
                            </x-weblebby::tabs>
                            <x-weblebby::card padding>
                                <x-weblebby::button type="submit">
                                    {{ $taxonomy ? __('Güncelle') : __('Oluştur') }}
                                </x-weblebby::button>
                            </x-weblebby::card>
                        </div>
                        <x-weblebby::form.sticky-submit />
                    </x-weblebby::form>
                </div>
            @endcan
            <div class="fd-space-y-3 fd-col-span-3">
                <x-weblebby::table>
                    <x-weblebby::table.head>
                        <x-weblebby::table.th>@lang('Başlık')</x-weblebby::table.th>
                        <x-weblebby::table.th>@lang('Üst :taxonomy', ['taxonomy' => $taxonomyItem->singularName()])</x-weblebby::table.th>
                        <x-weblebby::table.th>@lang('Değişiklik tarihi')</x-weblebby::table.th>
                        <x-weblebby::table.th />
                    </x-weblebby::table.head>
                    <x-weblebby::table.body>
                        @foreach ($taxonomies as $taxonomyValue)
                            <tr>
                                <x-weblebby::table.td class="fd-font-medium fd-text-lg">
                                    @can($taxonomyItem->abilityFor('update'))
                                        <a href="{{ panel_route('taxonomies.edit', [$taxonomyValue, 'locale' => request('locale')]) }}">{{ $taxonomyValue->term->title }}</a>
                                    @else
                                        <span>{{ $taxonomyValue->term->title }}</span>
                                    @endcan
                                </x-weblebby::table.td>
                                <x-weblebby::table.td>
                                    @if ($taxonomyValue->parent)
                                        @can($taxonomyItem->abilityFor('update'))
                                            <a href="{{ panel_route('taxonomies.edit', [$taxonomyValue->parent, 'locale' => request('locale')]) }}">{{ $taxonomyValue->parent->term->title }}</a>
                                        @else
                                            <span>{{ $taxonomyValue->parent->term->title }}</span>
                                        @endcan
                                    @else
                                        <span class="fd-text-zinc-400">-</span>
                                    @endif
                                </x-weblebby::table.td>
                                <x-weblebby::table.td>
                                    {{ Date::short($taxonomyValue->updated_at) }}
                                </x-weblebby::table.td>
                                <x-weblebby::table.td>
                                    <div class="fd-flex fd-items-center fd-gap-2 fd-ml-auto">
                                        @can($taxonomyItem->abilityFor('update'))
                                            <x-weblebby::button
                                                    as="a"
                                                    :href="panel_route('taxonomies.edit', [$taxonomyValue, 'locale' => request('locale')])"
                                                    size="sm"
                                                    variant="light"
                                            >
                                                @lang('Düzenle')
                                            </x-weblebby::button>
                                        @endcan
                                        @can($taxonomyItem->abilityFor('delete'))
                                            <x-weblebby::button
                                                    size="sm"
                                                    variant="red"
                                                    data-modal-open="#modal-delete-taxonomy"
                                                    :data-action="panel_route('taxonomies.destroy', $taxonomyValue)"
                                            >
                                                @lang('Sil')
                                            </x-weblebby::button>
                                        @endcan
                                    </div>
                                </x-weblebby::table.td>
                            </tr>
                        @endforeach
                    </x-weblebby::table.body>
                </x-weblebby::table>
                {{ $taxonomies->links() }}
            </div>
        </div>
    </x-weblebby::page>
    @can($taxonomyItem->abilityFor('delete'))
        <x-weblebby::modal.destroy
                id="modal-delete-taxonomy"
                :title="__(':name siliyorsunuz', ['name' => $taxonomyItem->singularName()])"
        />
    @endcan
</x-weblebby::layouts.panel>
