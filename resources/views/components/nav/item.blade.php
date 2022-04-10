@props(['icon', 'badge', 'active' => false])

<a {{ $attributes
    ->class('fd-flex fd-items-center fd-gap-3 fd-flex-1 fd-px-4 fd-py-1.5 fd-rounded-r-full fd-font-medium')
    ->class($active ? 'fd-bg-zinc-800 fd-text-zinc-200' : 'fd-text-zinc-600 hover:fd-bg-zinc-300 fd-transition-colors') }}>
    @if ($icon ?? null)
        <x-dynamic-component component="feadmin::icons.{{ $icon }}" class="fd-w-5 fd-h-5" />
    @endif
    <span>{{ $slot }}</span>
    @if ($badge ?? null)
        <span class="fd-ml-auto fd-text-xs fd-flex fd-items-center fd-justify-center fd-min-w-[1.25rem] fd-h-5 fd-p-1 fd-text-white fd-rounded-full fd-uppercase fd-font-bold {{ $active ? 'fd-bg-zinc-600' : 'fd-bg-red-400' }}">{{ $badge }}</span>
    @endif
</a>