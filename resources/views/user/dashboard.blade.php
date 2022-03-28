<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <div>
            <x-feadmin::page.title>@t('Ana Sayfa', 'panel')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Sevgili :name, yönetim paneline hoş geldin', 'admin', ['name' => auth()->user()->first_name])</x-feadmin::page.subtitle>
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