<x-weblebby::layouts.panel>
    <x-weblebby::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-weblebby::page.head :back="panel_route('users.index')">
            <x-weblebby::page.title>@lang('Create user')</x-weblebby::page.title>
            <x-weblebby::page.subtitle>@lang('Create a new user.')</x-weblebby::page.subtitle>
        </x-weblebby::page.head>
        <x-weblebby::form class="fd-space-y-3" :action="panel_route('users.store')">
            <x-weblebby::card class="fd-space-y-3" padding>
                <x-weblebby::form.group name="name">
                    <x-weblebby::form.label>@lang('Fullname')</x-weblebby::form.label>
                    <x-weblebby::form.input autofocus />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="email">
                    <x-weblebby::form.label>@lang('Email address')</x-weblebby::form.label>
                    <x-weblebby::form.input type="email" />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="role">
                    <x-weblebby::form.label>@lang('User role')</x-weblebby::form.label>
                    <x-weblebby::form.select>
                        <x-weblebby::form.option
                                selected
                                disabled
                        >@lang('Select user role')</x-weblebby::form.option>
                        @foreach ($roles as $role)
                            <x-weblebby::form.option :value="$role->id">{{ $role->name }}</x-weblebby::form.option>
                        @endforeach
                    </x-weblebby::form.select>
                </x-weblebby::form.group>
            </x-weblebby::card>
            <x-weblebby::button type="submit">@lang('Create')</x-weblebby::button>
            <x-weblebby::form.sticky-submit />
        </x-weblebby::form>
    </x-weblebby::page>
</x-weblebby::layouts.panel>