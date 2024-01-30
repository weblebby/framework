@props(['icon'])

<a {{ $attributes->class('fd-flex fd-items-center fd-gap-1 fd-text-zinc-600 hover:fd-text-zinc-800 fd-transition-colors') }}>
    @if ($icon ?? null)
        <x-dynamic-component component="weblebby::icons.{{ $icon }}" class="fd-w-4 fd-h-4" />
    @endif
    {{ $slot }}
</a>