<x-feadmin::layouts.master>
    <main class="flex-1 flex items-center justify-center">
        <div class="w-1/4 space-y-3">
            @if (session('status'))
                <x-feadmin::alert color="green">{{ session('status') }}</x-feadmin::alert>
            @endif
            <x-feadmin::card padding>
                <x-feadmin::form :action="route('password.email')">
                    <div class="space-y-3">
                        <h3 class="text-xl">@t('Parolamı unuttum', 'panel')</h3>
                        <x-feadmin::form.group name="email">
                            <x-feadmin::form.input type="email" :placeholder="t('e-Posta adresi', 'panel')" />
                        </x-feadmin::form.group>
                        <x-feadmin::button type="submit">@t('Sıfırlama bağlantısı gönder', 'panel')</x-feadmin::button>
                    </div>
                </x-feadmin::form>
            </x-feadmin::card>
            <div>
                <x-feadmin::link
                    :href="route('login')"
                    icon="chevron-left"
                >@t('Geri dön', 'panel')</x-feadmin::link>
            </div>
        </div>
    </main>
</x-feadmin::layouts.master>