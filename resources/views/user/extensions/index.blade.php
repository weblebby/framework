<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <x-feadmin::page.head>
            <x-feadmin::page.title>@lang('Eklentiler')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang('Eklentiler ile sitenizi zenginleştirin')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <div>
            <div class="fd-grid fd-grid-cols-4 fd-gap-3">
                @foreach (\Feadmin\Facades\Extension::getWithDeactivated() as $extension)
                    <x-feadmin::extension-card :extension="$extension" />
                @endforeach
            </div>
        </div>
    </x-feadmin::page>
</x-feadmin::layouts.panel>