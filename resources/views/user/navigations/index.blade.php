<x-feadmin::layouts.panel>
    <x-feadmin::page>
        @if ($selectedNavigation ?? null)
            <x-feadmin::page.head>
                <x-slot:actions>
                    @can('navigation:delete')
                        <x-feadmin::button
                                size="sm"
                                variant="red"
                                icon="x"
                                data-modal-open="#modal-delete-navigation"
                        >@lang('Sil')</x-feadmin::button>
                    @endcan
                </x-slot:actions>
                <x-feadmin::page.title>{{ $selectedNavigation->title }}</x-feadmin::page.title>
            </x-feadmin::page.head>
        @else
            <div>
                <x-feadmin::page.title>@lang('Menüler')</x-feadmin::page.title>
                <x-feadmin::page.subtitle>@lang('Sitenizin header gibi bölümlerinde yer alan menüleri yönetin')</x-feadmin::page.subtitle>
            </div>
        @endif
        <div>
            <div class="fd-grid fd-grid-cols-9 fd-gap-3">
                @if ($selectedNavigation ?? null)
                    @can('navigation:update')
                        <x-feadmin::form class="fd-col-span-3"
                                         :action="panel_route('navigations.update', $selectedNavigation)" method="PUT">
                            <x-feadmin::card class="fd-space-y-3" padding>
                                <x-feadmin::form.group name="title">
                                    <x-feadmin::form.label>@lang('Başlık')</x-feadmin::form.label>
                                    <x-feadmin::form.input
                                            :placeholder="__('örn. Ana menü')"
                                            :default="$selectedNavigation->title"
                                    />
                                </x-feadmin::form.group>
                                <x-feadmin::form.group name="handle">
                                    <x-feadmin::form.label>@lang('Tanımlayıcı')</x-feadmin::form.label>
                                    <x-feadmin::form.input
                                            :placeholder="__('örn. ana-menu')"
                                            :default="$selectedNavigation->handle"
                                    />
                                </x-feadmin::form.group>
                                <x-feadmin::button type="submit" size="sm">@lang('Kaydet')</x-feadmin::button>
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
                                <x-feadmin::dd.create-button>{{ __('Yeni öğe ekle') }}</x-feadmin::dd.create-button>
                            @endcan
                        </x-feadmin::card>
                    </div>
                @else
                    <div class="fd-col-span-7">
                        <x-feadmin::empty
                                icon="plus"
                                :title="__('Menü oluşturun veya seçin')"
                                :content="__('Öğeleri yönetmek için bir menü seçin')"
                        />
                    </div>
                @endif
                <div class="fd-col-span-2 fd-space-y-3">
                    @if ($navigations->isNotEmpty())
                        <x-feadmin::link-card>
                            @foreach ($navigations as $navigation)
                                <x-feadmin::link-card.item
                                        :href="panel_route('navigations.show', $navigation)"
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
                            >@lang('Yeni menü')</x-feadmin::link-card.item>
                        </x-feadmin::link-card>
                    @endcan
                </div>
            </div>
        </div>
    </x-feadmin::page>
    @if ($selectedNavigation ?? null)
        <x-feadmin::drawer id="drawer-create-menu-item">
            <x-feadmin::drawer.header :title="__('Yeni öğe ekle')" />
            <x-feadmin::form
                    :action="panel_route('navigations.items.store', $selectedNavigation)"
                    bag="item"
                    class="fd-space-y-3"
                    :data-edit-action="panel_route('navigations.items.update', [$selectedNavigation, ':id'])"
            >
                <input type="hidden" name="parent_id" value={{ old('parent_id') }}>
                <x-feadmin::form.group name="title">
                    <x-feadmin::form.label>@lang('Menü başlığı')</x-feadmin::form.label>
                    <x-feadmin::form.input data-drawer-focus />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="is_smart_menu">
                    <x-feadmin::form.checkbox :label="__('Otomatik menü')" value="1" />
                </x-feadmin::form.group>
                <div class="fd-space-y-3" data-smart-item>
                    <x-feadmin::form.group name="smart_type">
                        <x-feadmin::form.label>@lang('Otomatik menü')</x-feadmin::form.label>
                        <x-feadmin::form.select>
                            <x-feadmin::form.option value="" selected
                                                    disabled>@lang('Menü seçiniz')</x-feadmin::form.option>
                            @foreach (SmartMenu::items() as $item)
                                <x-feadmin::form.option
                                        value="{{ $item->name() }}">{{ $item->title() }}</x-feadmin::form.option>
                            @endforeach
                        </x-feadmin::form.select>
                    </x-feadmin::form.group>
                    <x-feadmin::form.group name="smart_condition">
                        <x-feadmin::form.label>@lang('Filtrele')</x-feadmin::form.label>
                        <x-feadmin::form.select />
                    </x-feadmin::form.group>
                    <x-feadmin::form.group name="smart_filters">
                        <x-feadmin::form.label>@lang('Kategoriler')</x-feadmin::form.label>
                        <x-feadmin::form.tagify :options="[]" />
                    </x-feadmin::form.group>
                    <x-feadmin::form.group name="smart_limit">
                        <x-feadmin::form.label>@lang('Limit')</x-feadmin::form.label>
                        <x-feadmin::form.input default="5" />
                    </x-feadmin::form.group>
                    <x-feadmin::form.group name="smart_view_all">
                        <x-feadmin::form.checkbox :label="__('Tümünü gör bağlantısını göster')" value="1" />
                    </x-feadmin::form.group>
                </div>
                <div class="fd-space-y-3" data-custom-item>
                    <x-feadmin::form.group name="linkable">
                        <x-feadmin::form.label>@lang('Bağlantı türü')</x-feadmin::form.label>
                        <x-feadmin::form.select>
                            <x-feadmin::form.option value="">@lang('Özel bağlantı')</x-feadmin::form.option>
                            <x-feadmin::form.option value="homepage">@lang('Ana sayfa')</x-feadmin::form.option>
                            @foreach (NavigationLinkable::linkables() as $linkable)
                                @if (count($linkable->links()) > 0)
                                    <optgroup label="{{ $linkable->title() }}">
                                        @foreach ($linkable->links() as $link)
                                            <x-feadmin::form.option
                                                    value="{{ json_encode(['linkable_id' => $link->id, 'linkable_type' => $linkable->model()]) }}">{{ $link->title }}</x-feadmin::form.option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </x-feadmin::form.select>
                    </x-feadmin::form.group>
                    <x-feadmin::form.group name="link">
                        <x-feadmin::form.label>@lang('Menü bağlantısı')</x-feadmin::form.label>
                        <x-feadmin::form.input placeholder="https://" />
                    </x-feadmin::form.group>
                </div>
                <x-feadmin::form.group name="open_in_new_tab">
                    <x-feadmin::form.checkbox :label="__('Yeni sekmede aç')" value="1" />
                </x-feadmin::form.group>
                <x-feadmin::form.group name="is_active">
                    <x-feadmin::form.checkbox :label="__('Aktif')" :default="true" value="1" />
                </x-feadmin::form.group>
                <x-feadmin::button type="submit">@lang('Kaydet')</x-feadmin::button>
            </x-feadmin::form>
        </x-feadmin::drawer>
        <x-feadmin::modal.destroy
                id="modal-delete-item"
                :title="__('Menü öğesini sil')"
                :subtitle="__('Bu ve (eğer varsa) altındaki öğeler kalıcı olarak silinecektir.')"
        />
        <x-feadmin::modal.destroy
                id="modal-delete-navigation"
                :title="__('Menüyü sil')"
                :action="panel_route('navigations.destroy', $selectedNavigation)"
        />
    @endif
    <x-feadmin::drawer id="drawer-create-navigation">
        <x-feadmin::drawer.header :title="__('Yeni menü')" />
        <x-feadmin::form class="fd-space-y-3" :action="panel_route('navigations.store')">
            <x-feadmin::form.group name="title">
                <x-feadmin::form.label>@lang('Başlık')</x-feadmin::form.label>
                <x-feadmin::form.input
                        :placeholder="__('örn. Ana menü')"
                        data-drawer-focus
                />
            </x-feadmin::form.group>
            <x-feadmin::form.group name="handle">
                <x-feadmin::form.label>@lang('Tanımlayıcı')</x-feadmin::form.label>
                <x-feadmin::form.input :placeholder="__('örn. ana-menu')" />
            </x-feadmin::form.group>
            <x-feadmin::button type="submit">@lang('Oluştur')</x-feadmin::button>
        </x-feadmin::form>
    </x-feadmin::drawer>

    @push('after_scripts')
        @vite('resources/js/navigation.js', 'feadmin')
        <script>
            @if ($errors->item->any())
            document.addEventListener("DOMContentLoaded", function() {
              Feadmin.Drawer.open(document.getElementById("drawer-create-menu-item"), {
                hasError: true,
                item: @json(old()) });
            });
            @endif

            @if ($selectedNavigation ?? null)
            document.addEventListener("DOMContentLoaded", function() {
              Feadmin.Navigation.init({{ $selectedNavigation->id }});
            });
            @endif
        </script>
    @endpush
</x-feadmin::layouts.panel>
