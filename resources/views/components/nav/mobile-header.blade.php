<div {{ $attributes->class('fd-flex fd-items-center fd-gap-1') }}>
    <button class="fd-p-1 fd-rounded hover:fd-bg-zinc-200" data-drawer="#drawer-mobile-nav">
        <x-feadmin::icons.list class="fd-w-8 fd-h-8" />
    </button>
    <div class="fd-text-lg fd-font-medium fd-truncate">{{ preference('general->site_name') }}</div>
</div>