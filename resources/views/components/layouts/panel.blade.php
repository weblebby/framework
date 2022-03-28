<x-feadmin::layouts.master>
    <x-slot name="beforeStyles">{{ $beforeStyles ?? '' }}</x-slot>
    <x-slot name="afterStyles">{{ $afterStyles ?? '' }}</x-slot>
    <x-slot name="scripts">{{ $scripts ?? '' }}</x-slot>
    <main class="flex flex-1">
        <section class="fixed inset-y-0 z-30 hidden md:flex flex-col w-60 h-full gap-8 bg-zinc-200 border-r border-zinc-300">
            <div class="flex flex-col">
                <a href="/" class="px-4 py-3 bg-zinc-50 border-b border-zinc-200">
                    <div class="uppercase font-bold text-sm text-zinc-700 truncate">{{ config('app.name') }}</div>
                </a>
                <div class="px-4 py-2 bg-zinc-100 border-b border-zinc-300">
                    <div class="text-zinc-800 font-medium">{{ auth()->user()->full_name }}</div>
                    <div class="text-sm text-zinc-500">{{ auth()->user()->getRoleNames()->first() }}</div>
                </div>
            </div>
            <x-feadmin::nav.menu-items />
            <x-feadmin::nav.footer />
        </section>
        <section class="fixed inset-y-0 z-30 flex md:hidden items-center px-4 w-full h-16 bg-zinc-100 border-b border-zinc-300">
            <x-feadmin::nav.mobile-header />
        </section>
        <nav id="drawer-mobile-nav" class="drawer drawer--half drawer--from-left p-0">
            <x-feadmin::nav.mobile-header class="p-2" />
            <x-feadmin::nav.menu-items />
            <x-feadmin::nav.footer />
        </nav>
        <div class="w-full mt-16 md:mt-0 md:ml-60">
            {{ $slot }}
        </div>
    </main>
</x-feadmin::layouts.master>