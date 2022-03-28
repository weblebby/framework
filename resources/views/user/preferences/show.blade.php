<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <div>
            <x-feadmin::page.title>@t('Ayarlar', 'panel')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@t('Sitenizin tüm ayarlarını buradan yönetin', 'panel')</x-feadmin::page.subtitle>
        </div>
        <div>
            <div class="fd-grid fd-grid-cols-5 fd-gap-3">
                <div>
                    <x-feadmin::link-card>
                        @foreach (Feadmin::panel()->preferences($namespace)->get() as $id => $bag)
                            <x-feadmin::link-card.item
                                href="{{ route('admin::preferences.show', $id) }}"
                                :active="$selectedBag === $id">
                                {{ $bag['title'] }}
                            </x-feadmin::link-card.item>
                        @endforeach
                    </x-feadmin::link-card>
                </div>
                <div class="fd-col-span-4">
                    <x-feadmin::card padding>
                        <x-feadmin::form :action="route('admin::preferences.update', $selectedBag)" method="PUT" enctype="multipart/form-data">
                            <div class="fd-space-y-3">
                                @foreach (Feadmin::panel()->preferences($namespace)->fields($selectedBag) as $field)
                                    <x-feadmin::form.field :field="$field" />
                                @endforeach
                                <x-feadmin::button type="submit">@t('Kaydet', 'panel')</x-feadmin::button>
                            </div>
                        </x-feadmin::form>
                    </x-feadmin::card>
                </div>
            </div>
        </div>
    </x-feadmin::page>
</x-feadmin::layouts.panel>