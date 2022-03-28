<div class="px-4">
    <div class="text-zinc-400">
        <div class="text-sm">v{{ Feadmin::version() }}</div>
        <a href="https://www.weblebby.com" target="_blank" class="text-xs">@t('powered by weblebby', 'driver')</a>
    </div>
    <x-feadmin::form :action="route('logout')" class="text-sm mt-4">
        <x-feadmin::button
            type="submit"
            variant="outline-light"
            size="sm"
        >@t('Oturumu kapat', 'admin')</x-feadmin::button>
    </x-feadmin::form>
</div>