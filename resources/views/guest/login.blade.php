<x-feadmin::layouts.master>
    <main class="fd-flex-1 fd-flex fd-items-center fd-justify-center">
        <div class="fd-w-1/4 fd-space-y-3">
            @if (session('status'))
                <x-feadmin::alert color="green">{{ session('status') }}</x-feadmin::alert>
            @endif
            <x-feadmin::card padding>
                <x-feadmin::form :action="route('login')">
                    <div class="fd-space-y-3">
                        <h3 class="fd-text-xl">@t('Oturum açın', 'panel')</h3>
                        <x-feadmin::form.group name="email">
                            <x-feadmin::form.input type="email" :placeholder="t('e-Posta adresi', 'panel')" />
                        </x-feadmin::form.group>
                        <x-feadmin::form.group name="password">
                            <x-feadmin::form.input type="password" :placeholder="t('Parola', 'panel')" />
                        </x-feadmin::form.group>
                        <div class="fd-flex fd-items-center fd-justify-between fd-gap-2">
                            <x-feadmin::button type="submit">@t('Oturum aç', 'panel')</x-feadmin::button>
                            <x-feadmin::button
                                as="a"
                                variant="link"
                                :href="route('password.request')"
                            >@t('Parolamı unuttum', 'panel')</x-feadmin::button>
                        </div>
                    </div>
                </x-feadmin::form>
            </x-feadmin::card>
            <a href="{{ route('home') }}" class="fd-block fd-text-zinc-600">{{ preference('general->site_name') }}</a>
        </div>
    </main>
</x-feadmin::layouts.master>