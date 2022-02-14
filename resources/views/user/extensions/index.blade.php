<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <div>
            <x-feadmin::page.title>@t('Eklentiler', 'admin')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Eklentiler ile sitenizi zenginleştirin', 'admin')</x-feadmin::page.subtitle>
        </div>
        <div>
            <div class="grid grid-cols-4 gap-3">
                @foreach (extensions()->all() as $extension)
                    <x-feadmin::extension-card :extension="$extension" />
                @endforeach
            </div>
        </div>
    </x-feadmin::page>
</x-feadmin::layouts.panel>