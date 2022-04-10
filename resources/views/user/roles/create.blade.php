<x-feadmin::layouts.panel>
    <x-feadmin::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-feadmin::page.head :back="panel_route('roles.index')">
            <x-feadmin::page.title>@t('Rol oluşturun', 'panel')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Yeni bir kullanıcı rolü oluşturun', 'panel')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <x-feadmin::form class="fd-space-y-3" :action="panel_route('roles.store')">
            <x-feadmin::card class="fd-space-y-3" padding>
                <x-feadmin::form.group name="name">
                    <x-feadmin::form.label>@t('Rol adı', 'panel')</x-feadmin::form.label>
                    <x-feadmin::form.input :placeholder="t('örn: Editör', 'panel')" autofocus />
                </x-feadmin::form.group>
            </x-feadmin::card>
            <x-feadmin::card class="fd-space-y-5" padding>
                @foreach (panel()->permission()->get() as $key => $group)
                    <div>
                        <h3 class="fd-text-lg fd-font-medium fd-leading-none">{{ $group['title'] }}</h3>
                        @if ($group['description'] ?? null)
                            <span class="fd-text-zinc-600 fd-leading-none">{{ $group['description'] }}</span>
                        @endif
                        <div class="fd-flex fd-flex-col fd-gap-2 fd-mt-2">
                            @foreach ($group['permissions'] as $perm => $label)
                                <x-feadmin::form.group name="permissions[]">
                                    <x-feadmin::form.checkbox value="{{ $key }}:{{ $perm }}" :label="$label" />
                                </x-feadmin::form.group>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </x-feadmin::card>
            <x-feadmin::button type="submit">@t('Oluştur', 'panel')</x-feadmin::button>
            <x-feadmin::form.sticky-submit />
        </x-feadmin::form>
    </x-feadmin::page>
</x-feadmin::layouts.panel>