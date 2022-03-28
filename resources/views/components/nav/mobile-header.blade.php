<div {{ $attributes->class('flex items-center gap-1') }}>
    <button class="p-1 rounded hover:bg-zinc-200" data-drawer="#drawer-mobile-nav">
        <x-feadmin::icons.list class="w-8 h-8" />
    </button>
    <div class="text-lg font-medium truncate">{{ preference('default::general__site_name') }}</div>
</div>