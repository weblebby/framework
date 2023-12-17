@props(['taxonomies', 'taxonomyItem'])

<x-feadmin::form.group name="primary_taxonomy" class="fd-hidden fd-mb-3" data-primary-taxonomy-group>
    <x-feadmin::form.label>@lang('Birincil Kategori')</x-feadmin::form.label>
    <x-feadmin::form.select data-primary-taxonomy-select />
</x-feadmin::form.group>

<div class="fd-border fd-bg-zinc-100 fd-rounded fd-p-3 fd-space-y-1 fd-max-h-80 fd-overflow-y-auto">
    @forelse ($taxonomies as $taxonomy)
        <x-feadmin::taxonomies.item
                :name="sprintf('taxonomies[%s][]', $taxonomyItem->name())"
                :taxonomy="$taxonomy"
        />
    @empty
        <div class="fd-text-sm fd-text-zinc-500">
            @lang('Hiçbir :taxonomy bulunamadı.', ['taxonomy' => Str::lower($taxonomyItem->singularName())])
        </div>
    @endforelse
</div>