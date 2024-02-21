<x-weblebby::layouts.auth>
    @if (session('status'))
        <x-weblebby::alert color="green">{{ session('status') }}</x-weblebby::alert>
    @endif
    <x-weblebby::card padding>
        <x-weblebby::form :action="panel()->route('password.email')">
            <div class="fd-space-y-3">
                <h3 class="fd-text-xl">@lang('I forgot my password')</h3>
                <x-weblebby::form.group name="email">
                    <x-weblebby::form.input type="email" :placeholder="__('Email address')" />
                </x-weblebby::form.group>
                <x-weblebby::button type="submit">@lang('Send reset link')</x-weblebby::button>
            </div>
        </x-weblebby::form>
    </x-weblebby::card>
    <div>
        <x-weblebby::link
                :href="panel()->route('login')"
                icon="chevron-left"
        >@lang('Go back')</x-weblebby::link>
    </div>
</x-weblebby::layouts.auth>