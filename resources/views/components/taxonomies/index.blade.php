@props(['taxonomies'])

<x-feadmin::form.group class="fd-hidden fd-mb-3" data-primary-taxonomy-group>
    <x-feadmin::form.label>@lang('Birincil Kategori')</x-feadmin::form.label>
    <x-feadmin::form.select data-primary-taxonomy-select />
</x-feadmin::form.group>

<div class="fd-border fd-bg-zinc-100 fd-rounded fd-p-3 fd-space-y-1 fd-max-h-80 fd-overflow-y-auto">
    @foreach ($taxonomies as $taxonomy)
        <x-feadmin::taxonomies.item :taxonomy="$taxonomy" />
    @endforeach
</div>