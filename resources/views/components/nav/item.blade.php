@props(['icon', 'active' => false])

<a {{ $attributes
    ->class('fd-flex fd-items-center fd-gap-3 fd-flex-1 fd-px-4 fd-py-1.5 fd-rounded-r-full fd-font-medium')
    ->class($active ? 'fd-bg-zinc-800 fd-text-zinc-200' : 'fd-text-zinc-600 hover:fd-bg-zinc-300 fd-transition-colors') }}>
    @if ($icon ?? null)
        <x-dynamic-component component="feadmin::icons.{{ $icon }}" class="fd-w-5 fd-h-5" />
    @endif
    <span>{{ $slot }}</span>
</a>