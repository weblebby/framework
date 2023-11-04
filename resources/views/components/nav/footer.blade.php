<div class="fd-px-4">
    <div class="fd-text-zinc-400">
        <div class="fd-text-sm">v{{ Panel::version() }}</div>
        <a href="https://www.weblebby.com" target="_blank" class="fd-text-xs">@lang('powered by weblebby')</a>
    </div>
    <x-feadmin::form :action="route('logout')" class="fd-text-sm fd-mt-4">
        <x-feadmin::button
                type="submit"
                variant="outline-light"
                size="sm"
        >@lang('Oturumu kapat')</x-feadmin::button>
    </x-feadmin::form>
</div>
