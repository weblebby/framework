@props(['title'])

<div class="fd-flex fd-items-center fd-justify-between">
    <h3 class="fd-text-lg fd-font-medium">{{ $title }}</h3>
    <x-weblebby::button rounded="full" size="icon" variant="light" data-drawer-close>
        <x-weblebby::icons.x class="fd-w-4 fd-h-4" />
    </x-weblebby::button>
</div>