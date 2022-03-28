<x-feadmin::layouts.panel>
    <x-feadmin::page class="lg:w-2/3 fd-mx-auto">
        <x-feadmin::page.head :back="route('admin::users.index')">
            <x-feadmin::page.title>@t('Kullanıcı oluşturun', 'panel')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Yeni bir kullanıcı oluşturun', 'panel')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <x-feadmin::form class="fd-space-y-3" :action="route('admin::users.store')">
            <x-feadmin::card class="fd-space-y-3" padding>
                <x-feadmin::form.group name="first_name">
                    <x-feadmin::form.label>@t('Ad', 'panel')</x-feadmin::form.label>
                    <x-feadmin::form.input autofocus />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="last_name">
                    <x-feadmin::form.label>@t('Soyad', 'panel')</x-feadmin::form.label>
                    <x-feadmin::form.input autofocus />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="email">
                    <x-feadmin::form.label>@t('e-Posta adresi', 'panel')</x-feadmin::form.label>
                    <x-feadmin::form.input type="email" />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="role">
                    <x-feadmin::form.label>@t('Kullanıcı rolü', 'panel')</x-feadmin::form.label>
                    <x-feadmin::form.select>
                        <x-feadmin::form.option selected disabled>@t('Kullanıcı rolü seçin', 'panel')</x-feadmin::form.option>
                        @foreach ($roles as $role)
                            <x-feadmin::form.option :value="$role->id">{{ $role->name }}</x-feadmin::form.option>
                        @endforeach
                    </x-feadmin::form.select>
                </x-feadmin::form.group>
            </x-feadmin::card>
            <x-feadmin::button type="submit">@t('Oluştur', 'panel')</x-feadmin::button>
            <x-feadmin::form.sticky-submit />
        </x-feadmin::form>
    </x-feadmin::page>
</x-feadmin::layouts.panel>