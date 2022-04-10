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
            >{{ $item['title'] }}</x-feadmin::nav.item>
        @endforeach
    </x-feadmin::nav>
@endforeach