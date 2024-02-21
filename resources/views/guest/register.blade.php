<x-weblebby::layouts.auth>
    @if (session('status'))
        <x-weblebby::alert color="green">{{ session('status') }}</x-weblebby::alert>
    @endif
    <x-weblebby::card padding>
        <x-weblebby::form :action="panel()->route('register')">
            <div class="fd-space-y-3">
                <h3 class="fd-text-xl">@lang('Create an account')</h3>
                <x-weblebby::form.group name="name">
                    <x-weblebby::form.input :placeholder="__('Fullname')" />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="email">
                    <x-weblebby::form.input type="email" :placeholder="__('Email address')" />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="password">
                    <x-weblebby::form.input type="password" :placeholder="__('Password')" />
                </x-weblebby::form.group>
                <div class="fd-flex fd-items-center fd-justify-between fd-gap-2">
                    <x-weblebby::button type="submit">@lang('Start')</x-weblebby::button>
                </div>
            </div>
        </x-weblebby::form>
    </x-weblebby::card>
    <x-weblebby::button
            as="a"
            variant="link"
            size="full"
            :href="panel()->route('login')"
    >@lang('Return to the login screen')</x-weblebby::button>
    <a href="{{ route('home') }}" class="fd-block fd-text-zinc-600 fd-text-center">
        {{ preference('general->site_name') }}
    </a>
</x-weblebby::layouts.auth>
