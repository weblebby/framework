<x-feadmin::layouts.master>
    <x-slot name="scripts">{{ $scripts ?? '' }}</x-slot>
    <main class="flex flex-1">
        <section class="fixed inset-y-0 z-30 flex flex-col w-60 py-12 pr-3 gap-8 bg-zinc-200 border-r border-zinc-300">
            <div class="px-4">
                <div class="text-lg font-medium mb-2">{{ auth()->user()->first_name }}</div>
                <x-feadmin::form :action="route('logout')">
                    <x-feadmin::button
                        type="submit"
                        variant="light"
                        size="sm"
                    >@t('Oturumu kapat', 'admin')</x-feadmin::button>
                </x-feadmin::form>
            </div>
            @foreach (Feadmin::currentPanel()->menus()->get('sidebar') as $category)
                <x-feadmin::nav>
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
            <div class="px-4 text-zinc-400">
                <div class="text-sm">v{{ Feadmin::version() }}</div>
                <div class="text-xs mt-3">weblebby.</div>
            </div>
        </section>
        <div class="w-full ml-60">
            {{ $slot }}
        </div>
    </main>
</x-feadmin::layouts.master>