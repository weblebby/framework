@props([
    'as' => 'a',
    'icon',
    'active' => false,
    'actions'
])

<div class="fd-relative fd-group">
    <{{ $as }} {{ $attributes
        ->class('fd-w-full fd-py-2 fd-px-4 fd-flex fd-items-center fd-gap-2')
        ->class($active ? 'fd-bg-zinc-100 fd-text-zinc-700 fd-font-medium' : 'hover:fd-bg-zinc-100 fd-transition-colors')
        ->class($as !== 'div' ? 'fd-cursor-pointer' : '') }}
    >
        @if ($icon ?? null)
            <x-dynamic-component
                component="feadmin::icons.{{ $icon }}"
                class="fd-text-zinc-800 fd-w-6 fd-h-6 fd-bg-zinc-200 fd-rounded-full fd-p-1"
            />
        @endif
        {{ $slot }}
    </{{ $as }}>
    @if ($actions ?? null)
        <div class="fd-absolute fd-right-4 fd-top-0 fd-bottom-0 fd-flex fd-items-center fd-gap-1 {{ !$active ? 'fd-opacity-0 group-hover:fd-opacity-100 fd-transition-opacity' : '' }}">
            {{ $actions }}
        </div>
    @endif
</div>