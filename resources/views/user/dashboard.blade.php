<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <div>
            <x-feadmin::page.title>@lang('Ana Sayfa')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang('Sevgili :name, yönetim paneline hoş geldin', ['name' => auth()->user()->first_name])</x-feadmin::page.subtitle>
        </div>
        <div>
            <div class="fd-grid fd-grid-cols-4 fd-gap-4">
                <x-feadmin::card padding>Selam</x-feadmin::card>
                <x-feadmin::card padding>Selam</x-feadmin::card>
                <x-feadmin::card padding>Selam</x-feadmin::card>
                <x-feadmin::card padding>Selam</x-feadmin::card>
            </div>
        </div>
    </x-feadmin::page>
</x-feadmin::layouts.panel>