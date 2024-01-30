@props([
    'primary',
    'taxonomies',
    'taxonomyItem'
])

@aware(['for'])

<div data-taxonomy-field>
    <x-weblebby::form.group
            :name="sprintf('primary_%s', $for ?? $taxonomyItem->name())"
            class="fd-hidden fd-mb-3"
            data-primary-taxonomy-group
    >
        <x-weblebby::form.label>@lang('Birincil :taxonomy', ['taxonomy' => \Illuminate\Support\Str::title($taxonomyItem->singularName())])</x-weblebby::form.label>
        <x-weblebby::form.select :data-primary-taxonomy-select="json_encode(($primary ?? null)?->id ?? null)">
            <option selected disabled>@lang('Seçiniz')</option>
        </x-weblebby::form.select>
    </x-weblebby::form.group>

    <div class="fd-border fd-bg-zinc-100 fd-rounded fd-p-3 fd-space-y-1 fd-max-h-80 fd-overflow-y-auto">
        @forelse ($taxonomies as $taxonomy)
            <x-weblebby::taxonomies.item
                    :name="sprintf('taxonomies[%s][]', $taxonomyItem->name())"
                    :taxonomy="$taxonomy"
            />
        @empty
            <div class="fd-text-sm fd-text-zinc-500">
                @lang('Hiçbir :taxonomy bulunamadı.', ['taxonomy' => Str::lower($taxonomyItem->singularName())])
            </div>
        @endforelse
    </div>
</div>