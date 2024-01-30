<x-weblebby::layouts.panel>
    <x-weblebby::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-weblebby::page.head :back="panel_route('users.index')">
            <x-weblebby::page.title>{{ $user->name }}</x-weblebby::page.title>
        </x-weblebby::page.head>
        <x-weblebby::form class="fd-space-y-3" :bind="$user" :action="panel_route('users.update', $user)" method="PUT">
            <x-weblebby::card class="fd-space-y-3" padding>
                <x-weblebby::form.group name="name">
                    <x-weblebby::form.label>@lang('İsim')</x-weblebby::form.label>
                    <x-weblebby::form.input autofocus />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="email">
                    <x-weblebby::form.label>@lang('e-Posta adresi')</x-weblebby::form.label>
                    <x-weblebby::form.input type="email" />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="role">
                    <x-weblebby::form.label>@lang('Kullanıcı rolü')</x-weblebby::form.label>
                    <x-weblebby::form.select :default="$user->roles()->first()->id">
                        <x-weblebby::form.option selected
                                                 disabled>@lang('Kullanıcı rolü seçin')</x-weblebby::form.option>
                        @foreach ($roles as $role)
                            <x-weblebby::form.option :value="$role->id">{{ $role->name }}</x-weblebby::form.option>
                        @endforeach
                    </x-weblebby::form.select>
                </x-weblebby::form.group>
            </x-weblebby::card>
            <x-weblebby::button type="submit">@lang('Oluştur')</x-weblebby::button>
            <x-weblebby::form.sticky-submit />
        </x-weblebby::form>
    </x-weblebby::page>
</x-weblebby::layouts.panel>