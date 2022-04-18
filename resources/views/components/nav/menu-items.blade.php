@foreach (panel()->menu('sidebar')->get() as $category)
    <x-feadmin::nav class="fd-pr-3">
        @if ($category['title'])
            <x-feadmin::nav.title>{{ $category['title'] }}</x-feadmin::nav.title>
        @endif
        @foreach ($category['items'] as $item)
            <x-feadmin::nav.item
                :icon="$item['icon'] ?? null"
                :badge="$item['badge'] ?? null"
                :href="$item['url']"
                :active="$item['is_active']"
            >
            @if ($item['children'])
                <x-slot:children>
                    @foreach ($item['children'] as $child)
                        <x-feadmin::nav.sub-item
                            :href="$child['url']"
                            :active="$child['is_active']"
                        >{{ $child['title'] }}</x-feadmin::nav.sub-item>
                    @endforeach
                </x-slot:children>
            @endif
            {{ $item['title'] }}
        </x-feadmin::nav.item>
        @endforeach
    </x-feadmin::nav>
@endforeach