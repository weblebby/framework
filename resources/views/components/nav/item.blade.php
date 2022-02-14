@props(['icon', 'active' => false])

<a {{ $attributes
    ->class('flex items-center gap-3 flex-1 px-4 py-1.5 rounded-r-full font-medium')
    ->class($active ? 'bg-zinc-800 text-zinc-200' : 'text-zinc-600 hover:bg-zinc-300 transition-colors') }}>
    @if ($icon ?? null)
        <x-dynamic-component component="feadmin::icons.{{ $icon }}" class="w-5 h-5" />
    @endif
    <span>{{ $slot }}</span>
</a>