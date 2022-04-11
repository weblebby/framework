<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <div>
            <x-feadmin::page.title>@t('Eklentiler', 'panel')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Eklentiler ile sitenizi zenginleştirin', 'panel')</x-feadmin::page.subtitle>
        </div>
        <div>
            <div class="fd-grid fd-grid-cols-4 fd-gap-3">
                @foreach (Extension::get() as $extension)
                    <x-feadmin::extension-card :extension="$extension" />
                @endforeach
            </div>
        </div>
    </x-feadmin::page>
</x-feadmin::layouts.panel>