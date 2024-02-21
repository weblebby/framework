<x-weblebby::layouts.panel>
    <x-weblebby::page>
        <x-weblebby::page.head>
            <x-weblebby::page.title>@lang('Dashboard')</x-weblebby::page.title>
            <x-weblebby::page.subtitle>@lang('Dear :name, welcome to the administration panel.', ['name' => auth()->user()->first_name])</x-weblebby::page.subtitle>
        </x-weblebby::page.head>
        <div class="fd-grid fd-gap-3 md:fd-grid-cols-2 lg:fd-grid-cols-3 xl:fd-grid-cols-4">
            <x-ext-visitors::user.current-month-visitors-chart />
            <x-ext-visitors::user.online-visitors-chart />
        </div>
    </x-weblebby::page>
</x-weblebby::layouts.panel>