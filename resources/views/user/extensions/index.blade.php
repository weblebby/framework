<x-weblebby::layouts.panel>
    <x-weblebby::page>
        <x-weblebby::page.head>
            <x-weblebby::page.title>@lang('Eklentiler')</x-weblebby::page.title>
            <x-weblebby::page.subtitle>@lang('Eklentiler ile sitenizi zenginleştirin')</x-weblebby::page.subtitle>
        </x-weblebby::page.head>
        <div>
            <div class="fd-grid fd-grid-cols-4 fd-gap-3">
                @foreach (\Weblebby\Framework\Facades\Extension::getWithDeactivated() as $extension)
                    <x-weblebby::extension-card :extension="$extension" />
                @endforeach
            </div>
        </div>
    </x-weblebby::page>
</x-weblebby::layouts.panel>