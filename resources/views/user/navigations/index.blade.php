<x-feadmin::layouts.panel>
    <x-slot name="scripts">
        <script src="{{ mix('js/navigation.js', 'vendor/feadmin') }}"></script>
        <script>
            @if ($errors->item->any())
                Drawer.open(document.getElementById('drawer-create-menu-item'), { isError: true })
            @endif

            @if ($selectedNavigation ?? null)
                document.addEventListener('DOMContentLoaded', () => {
                    Navigation.init({{ $selectedNavigation->id }})
                })
            @endif
        </script>
    </x-slot>
    <x-feadmin::page>
        @if ($selectedNavigation ?? null)
            <x-feadmin::page.head>
                <x-slot name="actions">
                    @can('navigation:delete')
                        <x-feadmin::button
                            size="sm"
                            variant="red"
                            icon="x"
                            data-modal-open="#modal-delete-navigation"
                        >@t('Sil', 'panel')</x-feadmin::button>
                    @endcan
                </x-slot>
                <x-feadmin::page.title>{{ $selectedNavigation->title }}</x-feadmin::page.title>
            </x-feadmin::page.head>
        @else
            <div>
                <x-feadmin::page.title>@t('Menüler', 'panel')</x-feadmin::page.title>
                <x-feadmin::page.subtitle>@t('Sitenizin header gibi bölümlerinde yer alan menüleri yönetin', 'panel')</x-feadmin::page.subtitle>
            </div>
        @endif
        <div>
            <div class="fd-grid fd-grid-cols-9 fd-gap-3">
                @if ($selectedNavigation ?? null)
                    @can('navigation:update')
                        <x-feadmin::form class="fd-col-span-3" :action="route('admin::navigations.update', $selectedNavigation)" method="PUT">
                            <x-feadmin::card class="fd-space-y-3" padding>
                                <x-feadmin::form.group name="title">
                                    <x-feadmin::form.label>@t('Başlık', 'panel')</x-feadmin::form.label>
                                    <x-feadmin::form.input
                                        :placeholder="t('örn. Ana menü', 'panel')"
                                        :default="$selectedNavigation->title"
                                    />
                                </x-feadmin::form.group>
                                <x-feadmin::form.group name="handle">
                                    <x-feadmin::form.label>@t('Tanımlayıcı', 'panel')</x-feadmin::form.label>
                                    <x-feadmin::form.input
                                        :placeholder="t('örn. ana-menu', 'panel')"
                                        :default="$selectedNavigation->handle"
                                    />
                                </x-feadmin::form.group>
                                <x-feadmin::button type="submit" size="sm">@t('Kaydet', 'panel')</x-feadmin::button>
                            </x-feadmin::card>
                        </x-feadmin::form>
                    @endcan
                    <div class="fd-col-span-4">
                        <x-feadmin::card class="fd-overflow-hidden">
                            <div class="fd-max-h-[30rem] fd-overflow-auto">
                                <div class="dd">
                                    <x-feadmin::dd.tree
                                        :items="$selectedNavigation->items"
                                        :readonly="auth()->user()->cannot('navigation:update')"
                                    />
                                </div>
                            </div>
                            @can('navigation:update')
                                <x-feadmin::dd.create-button>{{ t('Yeni öğe ekle', 'panel') }}</x-feadmin::dd.create-button>
                            @endcan
                        </x-feadmin::card>
                    </div>
                @else
                    <div class="fd-col-span-7">
                        <x-feadmin::empty
                            icon="plus"
                            :title="t('Menü oluşturun veya seçin', 'panel')"
                            :content="t('Öğeleri yönetmek için bir menü seçin', 'panel')"
                        />
                    </div>
                @endif
                <div class="fd-col-span-2 fd-space-y-3">
                    @if ($navigations->isNotEmpty())
                        <x-feadmin::link-card>
                            @foreach ($navigations as $navigation)
                                <x-feadmin::link-card.item
                                    :href="route('admin::navigations.show', $navigation)"
                                    :active="$navigation->id === ($selectedNavigation->id ?? null)"
                                >{{ $navigation->title }}</x-feadmin::link-card.item>
                            @endforeach
                        </x-feadmin::link-card>
                    @endif
                    @can('navigation:create')
                        <x-feadmin::link-card>
                            <x-feadmin::link-card.item
                                as="button"
                                icon="plus"
                                data-drawer="#drawer-create-navigation"
                            >@t('Yeni menü', 'panel')</x-feadmin::link-card.item>
                        </x-feadmin::link-card>
                    @endcan
                </div>
            </div>
        </div>
    </x-feadmin::page>
    @if ($selectedNavigation ?? null)
        <x-feadmin::drawer id="drawer-create-menu-item">
            <x-feadmin::drawer.header :title="t('Yeni öğe ekle', 'panel')" />
            <x-feadmin::form
                :action="route('admin::navigations.items.store', $selectedNavigation)"
                bag="item"
                class="fd-space-y-3"
                :data-edit-action="route('admin::navigations.items.update', [$selectedNavigation, ':id'])"
            >
                <input type="hidden" name="parent_id" value={{ old('parent_id') }}>
                <x-feadmin::form.group name="title">
                    <x-feadmin::form.label>@t('Menü başlığı', 'panel')</x-feadmin::form.label>
                    <x-feadmin::form.input data-drawer-focus />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="is_smart_menu">
                    <x-feadmin::form.checkbox :label="t('Otomatik menü', 'panel')" />
                </x-feadmin::form.group>
                <div class="fd-space-y-3" data-smart-item>
                    <x-feadmin::form.group name="smart_type">
                        <x-feadmin::form.label>@t('Otomatik menü', 'panel')</x-feadmin::form.label>
                        <x-feadmin::form.select>
                            <x-feadmin::form.option value="" selected disabled>@t('Menü seçiniz', 'panel')</x-feadmin::form.option>
                            @foreach ($smartMenuItems as $item)
                                <x-feadmin::form.option value="{{ $item->id }}">{{ $item->plural_title }}</x-feadmin::form.option>
                            @endforeach
                        </x-feadmin::form.select>
                    </x-feadmin::form.group>
                    <x-feadmin::form.group name="smart_limit">
                        <x-feadmin::form.label>@t('Limit', 'panel')</x-feadmin::form.label>
                        <x-feadmin::form.input default="5" />
                    </x-feadmin::form.group>
                </div>
                <div class="fd-space-y-3" data-manuel-item>
                    <x-feadmin::form.group name="linkable">
                        <x-feadmin::form.label>@t('Bağlantı türü', 'panel')</x-feadmin::form.label>
                        <x-feadmin::form.select>
                            <x-feadmin::form.option value="">@t('Manuel bağlantı', 'panel')</x-feadmin::form.option>
                            <x-feadmin::form.option value="homepage">@t('Ana sayfa', 'panel')</x-feadmin::form.option>
                            @foreach (NavigationLinkableManager::linkables() as $linkable)
                                <optgroup label="{{ $linkable['title'] }}">
                                    @foreach ($linkable['links'] as $link)
                                        <x-feadmin::form.option value="{{ json_encode(['linkable_id' => $link->id, 'linkable_type' => $linkable['id']]) }}">{{ $link->title }}</x-feadmin::form.option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </x-feadmin::form.select>
                    </x-feadmin::form.group>
                    <x-feadmin::form.group name="link">
                        <x-feadmin::form.label>@t('Menü bağlantısı', 'panel')</x-feadmin::form.label>
                        <x-feadmin::form.input placeholder="https://" />
                    </x-feadmin::form.group>
                </div>
                <x-feadmin::form.group name="open_in_new_tab">
                    <x-feadmin::form.checkbox :label="t('Yeni sekmede aç', 'panel')" />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="is_active">
                    <x-feadmin::form.checkbox :label="t('Aktif', 'panel')" :default="true" />
                </x-feadmin::form.group>
                <x-feadmin::button type="submit">@t('Kaydet', 'panel')</x-feadmin::button>
            </x-feadmin::form>
        </x-feadmin::drawer>
        <x-feadmin::modal.destroy
            id="modal-delete-item"
            :title="t('Menü öğesini sil', 'panel')"
            :subtitle="t('Bu ve (eğer varsa) altındaki öğeler kalıcı olarak silinecektir.', 'panel')"
        />
        <x-feadmin::modal.destroy
            id="modal-delete-navigation"
            :title="t('Menüyü sil', 'panel')"
            :action="route('admin::navigations.destroy', $selectedNavigation)"
        />
    @endif
    <x-feadmin::drawer id="drawer-create-navigation">
        <x-feadmin::drawer.header :title="t('Yeni menü', 'panel')" />
        <x-feadmin::form class="fd-space-y-3" :action="route('admin::navigations.store')">
            <x-feadmin::form.group name="title">
                <x-feadmin::form.label>@t('Başlık', 'panel')</x-feadmin::form.label>
                <x-feadmin::form.input
                    :placeholder="t('örn. Ana menü', 'panel')"
                    data-drawer-focus
                />
            </x-feadmin::form.group>
            <x-feadmin::form.group name="handle">
                <x-feadmin::form.label>@t('Tanımlayıcı', 'panel')</x-feadmin::form.label>
                <x-feadmin::form.input :placeholder="t('örn. ana-menu', 'panel')" />
            </x-feadmin::form.group>
            <x-feadmin::button type="submit">@t('Oluştur', 'panel')</x-feadmin::button>
        </x-feadmin::form>
    </x-feadmin::drawer>
</x-feadmin::layouts.panel>