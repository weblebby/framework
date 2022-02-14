@props(['as' => 'a', 'icon', 'active' => false])

<{{ $as }} {{ $attributes
    ->class('py-2 px-4 flex items-center gap-2 cursor-pointer')
    ->class($active ? 'bg-zinc-100 text-zinc-700 font-medium' : 'hover:bg-zinc-100 transition-colors') }}>
    @if ($icon ?? null)
        <x-dynamic-component
            component="feadmin::icons.{{ $icon }}"
            class="text-zinc-800 w-6 h-6 bg-zinc-200 rounded-full p-1"
        />
    @endif
    {{ $slot }}
</{{ $as }}>