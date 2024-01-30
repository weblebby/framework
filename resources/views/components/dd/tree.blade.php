@props(['items', 'readonly' => false])

<ol class="dd-list {{ $readonly ? 'dd-nodrag' : '' }}">
    @foreach ($items as $item)
        <x-weblebby::dd.item :item="$item" :readonly="$readonly">
            @if ($item->children->isNotEmpty())
                <x-weblebby::dd.tree :items="$item->children" :readonly="$readonly" />
            @endif
        </x-weblebby::dd.item>
    @endforeach
    {{ $slot }}
</ol>