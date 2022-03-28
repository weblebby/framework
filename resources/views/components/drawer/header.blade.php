@props(['title'])

<div class="fd-flex fd-items-center fd-justify-between">
    <h3 class="fd-text-lg fd-font-medium">{{ $title }}</h3>
    <x-feadmin::button rounded="full" size="icon" variant="light" data-drawer-close>
        <x-feadmin::icons.x class="w-4 h-4" />
    </x-feadmin::button>
</div>