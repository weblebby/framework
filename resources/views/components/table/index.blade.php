@props(['header'])

<x-weblebby::card class="fd-overflow-hidden">
    @if ($header ?? null)
        <div {{ $header->attributes->class('fd-p-4 fd-border-b') }}>
            {{ $header }}
        </div>
    @endif
    <x-weblebby::overflow>
        <table {{ $attributes->class('fd-w-full fd-divide-y fd-divide-zinc-200') }}>
            {{ $slot }}
        </table>
    </x-weblebby::overflow>
</x-weblebby::card>