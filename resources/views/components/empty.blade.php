@props(['icon', 'title', 'content'])

<x-feadmin::card class="flex flex-col items-center justify-center h-96">
    @if ($icon ?? null)
        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-sky-50 p-2 text-sky-500 mb-6">
            <x-dynamic-component component="feadmin::icons.{{ $icon }}" class="w-8 h-8" />
        </div>
    @endif
    <h1 class="font-bold text-2xl">{{ $title }}</h1>
    <x-feadmin::page.subtitle>{{ $content }}</x-feadmin::page.subtitle>
</x-feadmin::card>