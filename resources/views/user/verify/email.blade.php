<x-weblebby::layouts.auth>
    @if (session('status') === 'verification-link-sent')
        <x-weblebby::alert color="green">@lang('Verification link sent.')</x-weblebby::alert>
    @endif
    <x-weblebby::card padding>
        <x-weblebby::card.title>@lang('Verify your email')</x-weblebby::card.title>
        <x-weblebby::card.subtitle
                class="fd-mt-2">@lang('We have sent you an email with a verification link to confirm that the email address you provided during registration belongs to you.')</x-weblebby::card.subtitle>
        <x-weblebby::form :action="route('verification.send')" class="fd-mt-4">
            <x-weblebby::button type="submit" variant="light">
                @lang('Send verification link')
            </x-weblebby::button>
        </x-weblebby::form>
    </x-weblebby::card>
    <a href="{{ route('home') }}" class="fd-block fd-text-zinc-600">{{ preference('general->site_name') }}</a>
</x-weblebby::layouts.auth>