<x-feadmin::overflow>
    <x-feadmin::card>
        <table {{ $attributes->class('fd-min-w-full fd-divide-y fd-divide-zinc-200 fd-overflow-hidden fd-rounded') }}>
            {{ $slot }}
        </table>
    </x-feadmin::card>
</x-feadmin::overflow>