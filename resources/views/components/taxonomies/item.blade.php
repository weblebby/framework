@aware(['bind' => null])
@props(['name', 'taxonomy'])

<div class="fd-text-sm">
    <x-feadmin::form.checkbox
            :name="$name"
            :value="$taxonomy->id"
            :default="$bind?->taxonomies?->pluck('id')"
            :label="$taxonomy->term->title"
            :data-taxonomy-checkbox="$taxonomy->term->title"
    />
    @if ($taxonomy->children->isNotEmpty())
        <div class="fd-ml-8 fd-space-y-1">
            @foreach ($taxonomy->children as $child)
                <x-feadmin::taxonomies.item :name="$name" :taxonomy="$child" />
            @endforeach
        </div>
    @endif
</div>