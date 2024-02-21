<x-weblebby::layouts.auth>
    @if (session('status'))
        <x-weblebby::alert color="green">{{ session('status') }}</x-weblebby::alert>
    @endif
    <x-weblebby::card padding>
        <x-weblebby::form :action="panel()->route('login')">
            <div class="fd-space-y-3">
                <h3 class="fd-text-xl">@lang('Sign in')</h3>
                <x-weblebby::form.group name="email">
                    <x-weblebby::form.input type="email" :placeholder="__('Email address')" />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="password">
                    <x-weblebby::form.input type="password" :placeholder="__('Password')" />
                </x-weblebby::form.group>
                <div class="fd-flex fd-items-center fd-justify-between fd-gap-2">
                    <x-weblebby::button type="submit">@lang('Sign In')</x-weblebby::button>
                    <x-weblebby::button
                            as="a"
                            variant="link"
                            :href="panel()->route('password.request')"
                    >@lang('Forgot password')</x-weblebby::button>
                </div>
            </div>
        </x-weblebby::form>
    </x-weblebby::card>
    @if (panel()->supports(\Weblebby\Framework\Support\Features::registration()))
        <x-weblebby::button
                as="a"
                variant="link"
                :href="panel()->route('register')"
                size="full"
        >@lang('Create new account')</x-weblebby::button>
    @endif
    <a href="{{ route('home') }}" class="fd-block fd-text-zinc-600 fd-text-center">
        {{ preference('general->site_name') }}
    </a>
</x-weblebby::layouts.auth>