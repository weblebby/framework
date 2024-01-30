<x-weblebby::layouts.master {{ $attributes }}>
    <main class="fd-h-full fd-flex fd-items-center fd-justify-center">
        <div class="fd-w-11/12 md:fd-w-2/4 lg:fd-w-1/3 xl:fd-w-1/4 fd-space-y-3">
            {{ $slot }}
        </div>
    </main>
</x-weblebby::layouts.master>