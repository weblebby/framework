<x-feadmin::overflow>
    <x-feadmin::card>
        <table {{ $attributes->class('min-w-full divide-y divide-zinc-200 overflow-hidden rounded') }}>
            {{ $slot }}
        </table>
    </x-feadmin::card>
</x-feadmin::overflow>