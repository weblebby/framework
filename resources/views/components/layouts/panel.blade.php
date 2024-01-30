<x-weblebby::layouts.master {{ $attributes }}>
    <main class="fd-flex fd-flex-1 fd-h-full">
        <section
                class="fd-fixed fd-inset-y-0 fd-z-30 fd-hidden md:fd-flex fd-flex-col fd-w-60 fd-h-full fd-gap-8 fd-bg-zinc-200 fd-border-r fd-border-zinc-300 fd-overflow-auto">
            <div class="fd-flex fd-flex-col">
                <a href="/" class="fd-px-4 fd-py-3 fd-bg-zinc-50 fd-border-b fd-border-zinc-200">
                    <div class="fd-uppercase fd-font-bold fd-text-sm fd-text-zinc-700 fd-truncate">{{ preference('general->site_name', 'WebLebby') }}</div>
                </a>
                <div class="fd-px-4 fd-py-2 fd-bg-zinc-100 fd-border-b fd-border-zinc-300">
                    <div class="fd-text-zinc-800 fd-font-medium">{{ auth()->user()->name }}</div>
                    <div class="fd-text-sm fd-text-zinc-500">{{ auth()->user()->getRoleNames()->first() }}</div>
                </div>
            </div>
            <x-weblebby::nav.menu-items />
            <x-weblebby::nav.footer />
        </section>
        <section
                class="fd-fixed fd-inset-y-0 fd-z-30 fd-flex md:fd-hidden fd-items-center fd-px-4 fd-w-full fd-h-16 fd-bg-zinc-100 fd-border-b fd-border-zinc-300">
            <x-weblebby::nav.mobile-header />
        </section>
        <nav id="drawer-mobile-nav" class="drawer drawer--half drawer--from-left fd-p-0">
            <x-weblebby::nav.mobile-header class="fd-p-2" />
            <x-weblebby::nav.menu-items />
            <x-weblebby::nav.footer />
        </nav>
        <div class="fd-w-full fd-mt-16 md:fd-mt-0 md:fd-ml-60">
            {{ $slot }}
        </div>
    </main>
</x-weblebby::layouts.master>