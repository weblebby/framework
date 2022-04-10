@props(['header'])

<x-feadmin::card>
    @if ($header ?? null)
        <div {{ $header->attributes->class('fd-p-4 fd-border-b') }}>
            {{ $header }}
        </div>
    @endif
    <x-feadmin::overflow>
        <table {{ $attributes->class('fd-min-w-full fd-divide-y fd-divide-zinc-200 fd-overflow-hidden fd-rounded') }}>
            {{ $slot }}
        </table>
    </x-feadmin::overflow>
</x-feadmin::card>