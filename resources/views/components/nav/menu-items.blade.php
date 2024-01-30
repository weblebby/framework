@foreach (panel()->menu('sidebar')->get() as $category)
    <x-weblebby::nav class="fd-pr-3">
        @if ($category['title'])
            <x-weblebby::nav.title>{{ $category['title'] }}</x-weblebby::nav.title>
        @endif
        @foreach ($category['items'] as $item)
            <x-weblebby::nav.item
                    :icon="$item['icon'] ?? null"
                    :badge="$item['badge'] ?? null"
                    :href="$item['url']"
                    :active="$item['is_active'] || count(array_filter($item['children'], fn ($child) => $child['is_active'])) > 0"
            >
                @if ($item['children'])
                    <x-slot:children>
                        @foreach ($item['children'] as $child)
                            <x-weblebby::nav.sub-item
                                    :href="$child['url']"
                                    :active="$child['is_active']"
                            >{{ $child['title'] }}</x-weblebby::nav.sub-item>
                        @endforeach
                    </x-slot:children>
                @endif
                {{ $item['title'] }}
            </x-weblebby::nav.item>
        @endforeach
    </x-weblebby::nav>
@endforeach