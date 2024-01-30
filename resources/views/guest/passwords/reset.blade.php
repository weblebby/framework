<x-weblebby::layouts.auth>
    @if (session('status'))
        <x-weblebby::alert color="green">{{ session('status') }}</x-weblebby::alert>
    @endif
    @error('email')
    <x-weblebby::alert color="red">{{ $message  }}</x-weblebby::alert>
    @enderror
    <x-weblebby::card padding>
        <x-weblebby::form :action="panel()->route('password.update')">
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <input type="hidden" name="email" value="{{ request('email') }}">
            <div class="fd-space-y-3">
                <h3 class="fd-text-xl">@lang('Yeni parolanızı girin')</h3>
                <x-weblebby::form.group name="password">
                    <x-weblebby::form.input type="password" :placeholder="__('Yeni parolanız')" autofocus />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="password_confirmation">
                    <x-weblebby::form.input type="password" :placeholder="__('Parolanızı onaylayın')" />
                </x-weblebby::form.group>
                <x-weblebby::button type="submit">@lang('Parolamı sıfırla')</x-weblebby::button>
            </div>
        </x-weblebby::form>
    </x-weblebby::card>
    <x-weblebby::button
            as="a"
            variant="link"
            size="full"
            :href="panel()->route('login')"
    >@lang('Oturum açma ekranına dön')</x-weblebby::button>
</x-weblebby::layouts.auth>
