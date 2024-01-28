<x-feadmin::layouts.auth>
    @if (session('status'))
        <x-feadmin::alert color="green">{{ session('status') }}</x-feadmin::alert>
    @endif
    <x-feadmin::card padding>
        <x-feadmin::form :action="panel()->route('password.update')">
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <input type="hidden" name="email" value="{{ request('email') }}">
            <div class="fd-space-y-3">
                <h3 class="fd-text-xl">@lang('Yeni parolanızı girin')</h3>
                <x-feadmin::form.group name="password">
                    <x-feadmin::form.input type="password" :placeholder="__('Yeni parolanız')" />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="password_confirmation">
                    <x-feadmin::form.input type="password" :placeholder="__('Parolanızı onaylayın')" />
                </x-feadmin::form.group>
                <x-feadmin::button type="submit">@lang('Parolamı sıfırla')</x-feadmin::button>
            </div>
        </x-feadmin::form>
    </x-feadmin::card>
</x-feadmin::layouts.auth>