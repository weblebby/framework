<x-feadmin::layouts.auth>
    @if (session('status'))
        <x-feadmin::alert color="green">{{ session('status') }}</x-feadmin::alert>
    @endif
    @error('email')
    <x-feadmin::alert color="red">{{ $message  }}</x-feadmin::alert>
    @enderror
    <x-feadmin::card padding>
        <x-feadmin::form :action="panel()->route('password.update')">
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <input type="hidden" name="email" value="{{ request('email') }}">
            <div class="fd-space-y-3">
                <h3 class="fd-text-xl">@lang('Yeni parolanızı girin')</h3>
                <x-feadmin::form.group name="password">
                    <x-feadmin::form.input type="password" :placeholder="__('Yeni parolanız')" autofocus />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="password_confirmation">
                    <x-feadmin::form.input type="password" :placeholder="__('Parolanızı onaylayın')" />
                </x-feadmin::form.group>
                <x-feadmin::button type="submit">@lang('Parolamı sıfırla')</x-feadmin::button>
            </div>
        </x-feadmin::form>
    </x-feadmin::card>
    <x-feadmin::button
        as="a"
        variant="link"
        size="full"
        :href="panel()->route('login')"
    >@lang('Oturum açma ekranına dön')</x-feadmin::button>
</x-feadmin::layouts.auth>
