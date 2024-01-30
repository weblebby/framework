<x-weblebby::layouts.auth>
    @if (session('status') === 'verification-link-sent')
        <x-weblebby::alert color="green">@lang('Doğrulama bağlantısı gönderildi.')</x-weblebby::alert>
    @endif
    <x-weblebby::card padding>
        <x-weblebby::card.title>@lang('e-Posta adresinizi doğrulayın')</x-weblebby::card.title>
        <x-weblebby::card.subtitle
                class="fd-mt-2">@lang('Kaydolurken yazdığınız e-Posta adresinin size ait olduğundan emin olmak için doğrulama bağlantısı olan bir posta gönderdik.')</x-weblebby::card.subtitle>
        <x-weblebby::form :action="route('verification.send')" class="fd-mt-4">
            <x-weblebby::button type="submit" variant="light">
                @lang('Doğrulama bağlantısını gönder')
            </x-weblebby::button>
        </x-weblebby::form>
    </x-weblebby::card>
    <a href="{{ route('home') }}" class="fd-block fd-text-zinc-600">{{ preference('general->site_name') }}</a>
</x-weblebby::layouts.auth>