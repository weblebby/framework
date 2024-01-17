<x-feadmin::layouts.panel>
    <x-feadmin::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-feadmin::page.head :back="panel_route('users.index')">
            <x-feadmin::page.title>@lang('Kullanıcı oluşturun')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang('Yeni bir kullanıcı oluşturun')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <x-feadmin::form class="fd-space-y-3" :action="panel_route('users.store')">
            <x-feadmin::card class="fd-space-y-3" padding>
                <x-feadmin::form.group name="name">
                    <x-feadmin::form.label>@lang('İsim')</x-feadmin::form.label>
                    <x-feadmin::form.input autofocus />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="email">
                    <x-feadmin::form.label>@lang('e-Posta adresi')</x-feadmin::form.label>
                    <x-feadmin::form.input type="email" />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="role">
                    <x-feadmin::form.label>@lang('Kullanıcı rolü')</x-feadmin::form.label>
                    <x-feadmin::form.select>
                        <x-feadmin::form.option selected disabled>@lang('Kullanıcı rolü seçin')</x-feadmin::form.option>
                        @foreach ($roles as $role)
                            <x-feadmin::form.option :value="$role->id">{{ $role->name }}</x-feadmin::form.option>
                        @endforeach
                    </x-feadmin::form.select>
                </x-feadmin::form.group>
            </x-feadmin::card>
            <x-feadmin::button type="submit">@lang('Oluştur')</x-feadmin::button>
            <x-feadmin::form.sticky-submit />
        </x-feadmin::form>
    </x-feadmin::page>
</x-feadmin::layouts.panel>