<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <div>
            <x-feadmin::page.title>@t('Görünüm', 'admin')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Temanızı özelleştirin', 'admin')</x-feadmin::page.subtitle>
        </div>
        <div>
            <div class="flex items-center gap-2 mb-5">
                <x-feadmin::button
                    as="a"
                    :href="route('admin::appearance.homepage')"
                    rounded="full"
                    :variant="request()->routeIs('admin::appearance.homepage') ? 'primary' : 'light'"
                    upper
                >@t('Ana sayfa bölümleri', 'admin')</x-feadmin::button>
                <x-feadmin::button
                    as="a"
                    :href="route('admin::appearance.colors')"
                    rounded="full"
                    :variant="request()->routeIs('admin::appearance.colors') ? 'primary' : 'light'"
                    upper
                >@t('Renkler', 'admin')</x-feadmin::button>
            </div>
            {{ $slot }}
        </div>
    </x-feadmin::page>
</x-feadmin::layouts.panel>