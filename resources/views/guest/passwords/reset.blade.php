<x-feadmin::layouts.master>
    <main class="flex-1 flex items-center justify-center">
        <div class="w-1/4 space-y-3">
            @if (session('status'))
                <x-feadmin::alert color="green">{{ session('status') }}</x-feadmin::alert>
            @endif
            <x-feadmin::card padding>
                <x-feadmin::form :action="route('password.update')">
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    <input type="hidden" name="email" value="{{ request('email') }}">
                    <div class="space-y-3">
                        <h3 class="text-xl">@t('Yeni parolanızı girin', 'panel')</h3>
                        <x-feadmin::form.group name="password">
                            <x-feadmin::form.input type="password" :placeholder="t('Yeni parolanız', 'panel')" />
                        </x-feadmin::form.group>
                        <x-feadmin::form.group name="password_confirmation">
                            <x-feadmin::form.input type="password" :placeholder="t('Parolanızı onaylayın', 'panel')" />
                        </x-feadmin::form.group>
                        <x-feadmin::button type="submit">@t('Parolamı sıfırla', 'panel')</x-feadmin::button>
                    </div>
                </x-feadmin::form>
            </x-feadmin::card>
        </div>
    </main>
</x-feadmin::layouts.master>