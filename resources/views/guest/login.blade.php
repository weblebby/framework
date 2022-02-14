<x-feadmin::layouts.master>
    <main class="flex-1 flex items-center justify-center">
        <div class="w-1/4 space-y-3">
            @if (session('status'))
                <x-feadmin::alert color="green">{{ session('status') }}</x-feadmin::alert>
            @endif
            <x-feadmin::card padding>
                <x-feadmin::form :action="route('login')">
                    <div class="space-y-3">
                        <h3 class="text-xl">@t('Oturum açın', 'admin')</h3>
                        <x-feadmin::form.group name="email">
                            <x-feadmin::form.input type="email" :placeholder="t('e-Posta adresi', 'admin')" />
                        </x-feadmin::form.group>
                        <x-feadmin::form.group name="password">
                            <x-feadmin::form.input type="password" :placeholder="t('Parola', 'admin')" />
                        </x-feadmin::form.group>
                        <div class="flex items-center justify-between gap-2">
                            <x-feadmin::button type="submit">@t('Oturum aç', 'admin')</x-feadmin::button>
                            <x-feadmin::button
                                as="a"
                                variant="link"
                                :href="route('password.request')"
                            >@t('Parolamı unuttum', 'admin')</x-feadmin::button>
                        </div>
                    </div>
                </x-feadmin::form>
            </x-feadmin::card>
            <a href="{{ route('home') }}" class="block text-zinc-600">{{ preference('core::general__site_name') }}</a>
        </div>
    </main>
</x-feadmin::layouts.master>