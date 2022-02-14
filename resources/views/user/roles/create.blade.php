<x-feadmin::layouts.panel>
    <x-feadmin::page class="w-2/3 mx-auto">
        <x-feadmin::page.head :back="route('admin::roles.index')">
            <x-feadmin::page.title>@t('Rol oluşturun', 'admin')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Yeni bir kullanıcı rolü oluşturun', 'admin')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <x-feadmin::form class="space-y-3" :action="route('admin::roles.store')">
            <x-feadmin::card class="space-y-3" padding>
                <x-feadmin::form.group name="name">
                    <x-feadmin::form.label>@t('Rol adı', 'admin')</x-feadmin::form.label>
                    <x-feadmin::form.input :placeholder="t('örn: Editör', 'admin')" autofocus />
                </x-feadmin::form.group>
            </x-feadmin::card>
            <x-feadmin::card class="space-y-5" padding>
                @foreach (Feadmin::currentPanel()->permissions()->get() as $key => $group)
                    <div>
                        <h3 class="text-lg font-medium leading-none">{{ $group['title'] }}</h3>
                        @if ($group['description'] ?? null)
                            <span class="text-zinc-600 leading-none">{{ $group['description'] }}</span>
                        @endif
                        <div class="flex flex-col gap-2 mt-2">
                            @foreach ($group['permissions'] as $perm => $label)
                                <x-feadmin::form.group name="permissions[]">
                                    <x-feadmin::form.checkbox value="{{ $key }}:{{ $perm }}" :label="$label" />
                                </x-feadmin::form.group>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </x-feadmin::card>
            <x-feadmin::button type="submit">@t('Oluştur', 'admin')</x-feadmin::button>
            <x-feadmin::form.sticky-submit />
        </x-feadmin::form>
    </x-feadmin::page>
</x-feadmin::layouts.panel>