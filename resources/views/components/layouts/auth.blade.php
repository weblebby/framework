<x-feadmin::layouts.master {{ $attributes }}>
    <x-slot:styles>{{ $styles ?? '' }}</x-slot:styles>
    <x-slot:scripts>{{ $scripts ?? '' }}</x-slot:scripts>
    <main class="fd-h-full fd-flex fd-items-center fd-justify-center">
        <div class="fd-w-11/12 md:fd-w-2/4 lg:fd-w-1/3 xl:fd-w-1/4 fd-space-y-3">
            {{ $slot }}
        </div>
    </main>
</x-feadmin::layouts.master>