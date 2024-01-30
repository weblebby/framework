<x-weblebby::layouts.auth>
    @if (session('status'))
        <x-weblebby::alert color="green">{{ session('status') }}</x-weblebby::alert>
    @endif
    <x-weblebby::card padding>
        <x-weblebby::form :action="panel()->route('password.email')">
            <div class="fd-space-y-3">
                <h3 class="fd-text-xl">@lang('Parolamı unuttum')</h3>
                <x-weblebby::form.group name="email">
                    <x-weblebby::form.input type="email" :placeholder="__('e-Posta adresi')" />
                </x-weblebby::form.group>
                <x-weblebby::button type="submit">@lang('Sıfırlama bağlantısı gönder')</x-weblebby::button>
            </div>
        </x-weblebby::form>
    </x-weblebby::card>
    <div>
        <x-weblebby::link
                :href="panel()->route('login')"
                icon="chevron-left"
        >@lang('Geri dön')</x-weblebby::link>
    </div>
</x-weblebby::layouts.auth>