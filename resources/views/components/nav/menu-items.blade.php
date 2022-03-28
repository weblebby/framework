@foreach (Feadmin::panel()->menus('sidebar')->get() as $category)
    <x-feadmin::nav class="fd-pr-3">
        @if ($category['title'])
            <x-feadmin::nav.title>{{ $category['title'] }}</x-feadmin::nav.title>
        @endif
        @foreach ($category['items'] as $item)
            <x-feadmin::nav.item
                :icon="$item['icon'] ?? null"
                :href="$item['url']"
                :active="$item['is_active']"
            >{{ $item['title'] }}</x-feadmin::nav.item>
        @endforeach
    </x-feadmin::nav>
@endforeach