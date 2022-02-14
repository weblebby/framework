<x-feadmin::layouts.panel>
    <x-feadmin::page class="{{ count($namespaces) <= 1 ? 'w-2/3 mx-auto' : '' }}">
        <x-feadmin::page.head :back="route('admin::extensions.index')">
            <x-feadmin::page.title>{{ $extension->plural_title }}</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t(':extension modülünü ayarlayın', 'admin', ['extension' => $extension->singular_title])</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <div>
            <div class="{{ count($namespaces) > 1 ? 'grid grid-cols-5 gap-3' : '' }}">
                @if(count($namespaces) > 1)
                    <div>
                        <x-feadmin::link-card>
                            @foreach ($namespaces as $id => $bag)
                                <x-feadmin::link-card.item
                                    href="{{ route('admin::extensions.preferences.show', [$extension->id, $id]) }}"
                                    :active="$selectedBag === $id">
                                    {{ $bag['title'] }}
                                </x-feadmin::link-card.item>
                            @endforeach
                        </x-feadmin::link-card>
                    </div>
                @endif
                <div class="col-span-4">
                    <x-feadmin::card padding>
                        <x-feadmin::form :action="route('admin::extensions.preferences.update', [$extension->id, $selectedBag])" method="PUT" enctype="multipart/form-data">
                            <div class="space-y-3">
                                @foreach (PreferenceManager::getFields($extension->id, $selectedBag) as $field)
                                    <x-feadmin::form.field :field="$field" />
                                @endforeach
                                <x-feadmin::button type="submit">@t('Kaydet', 'admin')</x-feadmin::button>
                            </div>
                        </x-feadmin::form>
                    </x-feadmin::card>
                </div>
            </div>
        </div>
    </x-feadmin::page>
</x-feadmin::layouts.panel>