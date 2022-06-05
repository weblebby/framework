<x-feadmin::layouts.auth>
    @if (session('status'))
        <x-feadmin::alert color="green">{{ session('status') }}</x-feadmin::alert>
    @endif
    <x-feadmin::card padding>
        <x-feadmin::form :action="route('login')" bag="login">
            <div class="fd-space-y-3">
                <h3 class="fd-text-xl">@lang('Oturum açın')</h3>
                <x-feadmin::form.group name="email">
                    <x-feadmin::form.input type="email" :placeholder="__('e-Posta adresi')" />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="password">
                    <x-feadmin::form.input type="password" :placeholder="__('Parola')" />
                </x-feadmin::form.group>
                <div class="fd-flex fd-items-center fd-justify-between fd-gap-2">
                    <x-feadmin::button type="submit">@lang('Oturum aç')</x-feadmin::button>
                    <x-feadmin::button
                        as="a"
                        variant="link"
                        :href="route('password.request')"
                    >@lang('Parolamı unuttum')</x-feadmin::button>
                </div>
            </div>
        </x-feadmin::form>
    </x-feadmin::card>
    <a href="{{ route('home') }}" class="fd-block fd-text-zinc-600">{{ preference('general->site_name') }}</a>
</x-feadmin::layouts.auth>