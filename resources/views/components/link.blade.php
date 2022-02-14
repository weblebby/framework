@props(['icon'])

<a {{ $attributes->class('flex items-center gap-1 text-zinc-600 hover:text-zinc-800 transition-colors') }}>
    @if ($icon ?? null)
        <x-dynamic-component component="feadmin::icons.{{ $icon }}" class="w-4 h-4" />
    @endif
    {{ $slot }}
</a>