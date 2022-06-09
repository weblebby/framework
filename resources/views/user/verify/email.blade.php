<x-feadmin::layouts.auth>
    @if (session('status') === 'verification-link-sent')
        <x-feadmin::alert color="green">@lang('Doğrulama bağlantısı gönderildi.')</x-feadmin::alert>
    @endif
    <x-feadmin::card padding>
        <x-feadmin::card.title>@lang('e-Posta adresinizi doğrulayın')</x-feadmin::card.title>
        <x-feadmin::card.subtitle class="fd-mt-2">@lang('Kaydolurken yazdığınız e-Posta adresinin size ait olduğundan emin olmak için doğrulama bağlantısı olan bir posta gönderdik.')</x-feadmin::card.subtitle>
        <x-feadmin::form :action="route('verification.send')" class="fd-mt-4">
            <x-feadmin::button type="submit" variant="light">
                @lang('Doğrulama bağlantısını gönder')
            </x-feadmin::button>
        </x-feadmin::form>
    </x-feadmin::card>
    <a href="{{ route('home') }}" class="fd-block fd-text-zinc-600">{{ preference('general->site_name') }}</a>
</x-feadmin::layouts.auth>