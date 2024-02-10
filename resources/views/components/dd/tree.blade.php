@props(['items', 'readonly' => false])

<ol @class(['dd-list', 'dd-nodrag' => $readonly])>
    @foreach ($items as $item)
        <x-weblebby::dd.item :item="$item" :readonly="$readonly" :disabled="!$item->is_active">
            @if ($item->children->isNotEmpty())
                <x-weblebby::dd.tree :items="$item->children" :readonly="$readonly" />
            @endif
        </x-weblebby::dd.item>
    @endforeach
    {{ $slot }}
</ol>