@props(['as' => 'a', 'icon', 'active' => false])

<{{ $as }} {{ $attributes
    ->class('fd-py-2 fd-px-4 fd-flex fd-items-center fd-gap-2 fd-cursor-pointer')
    ->class($active ? 'fd-bg-zinc-100 fd-text-zinc-700 fd-font-medium' : 'hover:fd-bg-zinc-100 vtransition-colors') }}>
    @if ($icon ?? null)
        <x-dynamic-component
            component="feadmin::icons.{{ $icon }}"
            class="fd-text-zinc-800 fd-w-6 fd-h-6 fd-bg-zinc-200 fd-rounded-full fd-p-1"
        />
    @endif
    {{ $slot }}
</{{ $as }}>