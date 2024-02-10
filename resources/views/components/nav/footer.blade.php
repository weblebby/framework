<div class="fd-px-4 fd-pb-4">
    <div class="fd-text-zinc-400">
        <div class="fd-text-sm">v{{ Panel::version() }}</div>
        @if (($domain = domain()) && $domain->account?->reseller)
            @if ($domain->account->reseller->url)
                <a href="{{ $domain->account->reseller->url }}" target="_blank" class="fd-text-xs">
                    @lang('powered by :reseller', ['reseller' => $domain->account->reseller->name])
                </a>
            @else
                <span class="fd-text-xs">
                    @lang('powered by :reseller', ['reseller' => $domain->account->reseller->name])
                </span>
            @endif
        @else
            <a href="https://www.weblebby.com" target="_blank" class="fd-text-xs">@lang('powered by weblebby')</a>
        @endif
    </div>
    <x-weblebby::form :action="panel()->route('logout')" class="fd-text-sm fd-mt-4">
        <x-weblebby::button
                type="submit"
                variant="outline-light"
                size="sm"
        >@lang('Oturumu kapat')</x-weblebby::button>
    </x-weblebby::form>
</div>
