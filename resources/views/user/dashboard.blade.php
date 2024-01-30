<x-weblebby::layouts.panel>
    <x-weblebby::page>
        <x-weblebby::page.head>
            <x-weblebby::page.title>@lang('Ana Sayfa')</x-weblebby::page.title>
            <x-weblebby::page.subtitle>@lang('Sevgili :name, yönetim paneline hoş geldin', ['name' => auth()->user()->first_name])</x-weblebby::page.subtitle>
        </x-weblebby::page.head>
    </x-weblebby::page>
</x-weblebby::layouts.panel>