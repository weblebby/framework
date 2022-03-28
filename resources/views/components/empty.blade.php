@props(['icon', 'title', 'content'])

<x-feadmin::card class="fd-flex fd-flex-col fd-items-center fd-justify-center fd-h-96">
    @if ($icon ?? null)
        <div class="fd-flex fd-items-center fd-justify-center fd-w-16 fd-h-16 fd-rounded-full fd-bg-sky-50 fd-p-2 fd-text-sky-500 fd-mb-6">
            <x-dynamic-component component="feadmin::icons.{{ $icon }}" class="fd-w-8 fd-h-8" />
        </div>
    @endif
    <h1 class="fd-font-bold fd-text-2xl">{{ $title }}</h1>
    <x-feadmin::page.subtitle>{{ $content }}</x-feadmin::page.subtitle>
</x-feadmin::card>