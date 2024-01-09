<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <x-feadmin::page.head>
            <x-feadmin::page.title>@lang('Ana Sayfa')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang('Sevgili :name, yönetim paneline hoş geldin', ['name' => auth()->user()->first_name])</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
    </x-feadmin::page>
</x-feadmin::layouts.panel>