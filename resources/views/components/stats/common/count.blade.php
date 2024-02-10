@props(['title', 'subtitle', 'variant' => 'default'])

<x-weblebby::card class="fd-overflow-hidden fd-flex fd-flex-col">
    <div @class(['fd-flex fd-items-center fd-justify-between fd-gap-3 fd-p-3 fd-text-center fd-bg-zinc-100'])>
        <h3 class="fd-font-bold">{{ $title }}</h3>
        @if ($subtitle ?? null)
            <p class="fd-text-zinc-500 fd-text-xs fd-uppercase fd-font-bold">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="fd-flex-1 fd-flex fd-items-center fd-justify-center fd-text-center fd-text-zinc-800 fd-min-h-16 fd-p-3 fd-border-t">
        {{ $slot }}
    </div>
</x-weblebby::card>