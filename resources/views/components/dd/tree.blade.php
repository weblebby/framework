@props(['items', 'readonly' => false])

<ol class="dd-list {{ $readonly ? 'dd-nodrag' : '' }}">
    @foreach ($items as $item)
        <x-feadmin::dd.item :item="$item" :readonly="$readonly">
            @if ($item->children->isNotEmpty())
                <x-feadmin::dd.tree :items="$item->children" :readonly="$readonly" />
            @endif
        </x-feadmin::dd.item>
    @endforeach
    {{ $slot }}
</ol>