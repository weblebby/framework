<x-weblebby::layouts.panel>
    <x-weblebby::page>
        @if ($selectedNavigation ?? null)
            <x-weblebby::page.head>
                <x-slot:actions>
                    @can('navigation:delete')
                        <x-weblebby::button
                                size="sm"
                                variant="red"
                                icon="x"
                                data-modal-open="#modal-delete-navigation"
                        >@lang('Sil')</x-weblebby::button>
                    @endcan
                </x-slot:actions>
                <x-weblebby::page.title>{{ $selectedNavigation->title }}</x-weblebby::page.title>
            </x-weblebby::page.head>
        @else
            <div>
                <x-weblebby::page.title>@lang('Menüler')</x-weblebby::page.title>
                <x-weblebby::page.subtitle>@lang('Sitenizin header gibi bölümlerinde yer alan menüleri yönetin')</x-weblebby::page.subtitle>
            </div>
        @endif
        <div>
            <div class="fd-grid fd-grid-cols-9 fd-gap-3">
                <div class="fd-col-span-2 fd-space-y-3">
                    @if ($navigations->isNotEmpty())
                        <x-weblebby::link-card>
                            @foreach ($navigations as $navigation)
                                <x-weblebby::link-card.item
                                        :href="panel_route('navigations.show', $navigation)"
                                        :active="$navigation->id === ($selectedNavigation->id ?? null)"
                                >{{ $navigation->title }}</x-weblebby::link-card.item>
                            @endforeach
                        </x-weblebby::link-card>
                    @endif
                    @can('navigation:create')
                        <x-weblebby::link-card>
                            <x-weblebby::link-card.item
                                    as="button"
                                    icon="plus"
                                    data-drawer="#drawer-create-navigation"
                            >@lang('Yeni menü')</x-weblebby::link-card.item>
                        </x-weblebby::link-card>
                    @endcan
                </div>
                @if ($selectedNavigation ?? null)
                    @can('navigation:update')
                        <x-weblebby::form class="fd-col-span-3"
                                          :action="panel_route('navigations.update', $selectedNavigation)" method="PUT">
                            <x-weblebby::card class="fd-space-y-3" padding>
                                <x-weblebby::form.group name="title">
                                    <x-weblebby::form.label>@lang('Başlık')</x-weblebby::form.label>
                                    <x-weblebby::form.input
                                            :placeholder="__('örn. Ana menü')"
                                            :default="$selectedNavigation->title"
                                    />
                                </x-weblebby::form.group>
                                <x-weblebby::form.group name="handle">
                                    <x-weblebby::form.label>@lang('Tanımlayıcı')</x-weblebby::form.label>
                                    <x-weblebby::form.input
                                            :placeholder="__('örn. ana-menu')"
                                            :default="$selectedNavigation->handle"
                                    />
                                </x-weblebby::form.group>
                                <x-weblebby::button type="submit" size="sm">@lang('Kaydet')</x-weblebby::button>
                            </x-weblebby::card>
                        </x-weblebby::form>
                    @endcan
                    <div class="fd-col-span-4">
                        <x-weblebby::card class="fd-overflow-hidden">
                            <div class="fd-max-h-[30rem] fd-overflow-auto">
                                <div class="dd">
                                    <x-weblebby::dd.tree
                                            :items="$selectedNavigation->items"
                                            :readonly="auth()->user()->cannot('navigation:update')"
                                    />
                                </div>
                            </div>
                            @can('navigation:update')
                                <x-weblebby::dd.create-button>{{ __('Yeni öğe ekle') }}</x-weblebby::dd.create-button>
                            @endcan
                        </x-weblebby::card>
                    </div>
                @else
                    <div class="fd-col-span-7">
                        <x-weblebby::empty
                                icon="plus"
                                :title="__('Menü oluşturun veya seçin')"
                                :content="__('Öğeleri yönetmek için bir menü seçin')"
                        />
                    </div>
                @endif
            </div>
        </div>
    </x-weblebby::page>
    @if ($selectedNavigation ?? null)
        <x-weblebby::drawer id="drawer-create-menu-item">
            <x-weblebby::drawer.header :title="__('Yeni öğe ekle')" />
            <x-weblebby::form
                    :action="panel_route('navigations.items.store', $selectedNavigation)"
                    bag="item"
                    class="fd-space-y-3"
                    :data-edit-action="panel_route('navigations.items.update', [$selectedNavigation, ':id'])"
            >
                <input type="hidden" name="_locale" value="{{ $locale }}">
                <input type="hidden" name="parent_id" value={{ old('parent_id') }}>
                <x-weblebby::form.group name="title">
                    <x-weblebby::form.label>@lang('Menü başlığı')</x-weblebby::form.label>
                    <x-weblebby::form.input data-drawer-focus :translatable="$isTranslatable" />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="is_smart_menu">
                    <x-weblebby::form.checkbox :label="__('Otomatik menü')" value="1" />
                </x-weblebby::form.group>
                <div class="fd-space-y-3" data-smart-item>
                    <x-weblebby::form.group name="smart_type">
                        <x-weblebby::form.label>@lang('Otomatik menü')</x-weblebby::form.label>
                        <x-weblebby::form.select>
                            <x-weblebby::form.option value="" selected
                                                     disabled>@lang('Menü seçiniz')</x-weblebby::form.option>
                            @foreach (SmartMenu::items() as $item)
                                <x-weblebby::form.option
                                        value="{{ $item->name() }}">{{ $item->title() }}</x-weblebby::form.option>
                            @endforeach
                        </x-weblebby::form.select>
                    </x-weblebby::form.group>
                    <x-weblebby::form.group name="smart_condition">
                        <x-weblebby::form.label>@lang('Filtrele')</x-weblebby::form.label>
                        <x-weblebby::form.select />
                    </x-weblebby::form.group>
                    <x-weblebby::form.group name="smart_filters">
                        <x-weblebby::form.label>@lang('Kategoriler')</x-weblebby::form.label>
                        <x-weblebby::form.tagify :options="[]" />
                    </x-weblebby::form.group>
                    <x-weblebby::form.group name="smart_limit">
                        <x-weblebby::form.label>@lang('Limit')</x-weblebby::form.label>
                        <x-weblebby::form.input default="5" />
                    </x-weblebby::form.group>
                    <x-weblebby::form.group name="smart_view_all">
                        <x-weblebby::form.checkbox :label="__('Tümünü gör bağlantısını göster')" value="1" />
                    </x-weblebby::form.group>
                </div>
                <div class="fd-space-y-3" data-custom-item>
                    <x-weblebby::form.group name="linkable">
                        <x-weblebby::form.label>@lang('Bağlantı türü')</x-weblebby::form.label>
                        <x-weblebby::form.select>
                            <x-weblebby::form.option value="">@lang('Özel bağlantı')</x-weblebby::form.option>
                            <x-weblebby::form.option value="homepage">@lang('Ana sayfa')</x-weblebby::form.option>
                            @foreach (NavigationLinkable::linkables() as $linkable)
                                @if (count($linkable->links()) > 0)
                                    <optgroup label="{{ $linkable->title() }}">
                                        @foreach ($linkable->links() as $link)
                                            <x-weblebby::form.option
                                                    value="{{ json_encode(['linkable_id' => $link->id, 'linkable_type' => $linkable->model()]) }}">{{ $link->title }}</x-weblebby::form.option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </x-weblebby::form.select>
                    </x-weblebby::form.group>
                    <x-weblebby::form.group name="link">
                        <x-weblebby::form.label>@lang('Menü bağlantısı')</x-weblebby::form.label>
                        <x-weblebby::form.input placeholder="https://" />
                    </x-weblebby::form.group>
                </div>
                <x-weblebby::form.group name="open_in_new_tab">
                    <x-weblebby::form.checkbox :label="__('Yeni sekmede aç')" value="1" />
                </x-weblebby::form.group>
                <x-weblebby::form.group name="is_active">
                    <x-weblebby::form.checkbox :label="__('Aktif')" :default="true" value="1" />
                </x-weblebby::form.group>
                <x-weblebby::button type="submit">@lang('Kaydet')</x-weblebby::button>
            </x-weblebby::form>
        </x-weblebby::drawer>
        <x-weblebby::modal.destroy
                id="modal-delete-item"
                :title="__('Menü öğesini sil')"
                :subtitle="__('Bu ve (eğer varsa) altındaki öğeler kalıcı olarak silinecektir.')"
        />
        <x-weblebby::modal.destroy
                id="modal-delete-navigation"
                :title="__('Menüyü sil')"
                :action="panel_route('navigations.destroy', $selectedNavigation)"
        />
    @endif
    <x-weblebby::drawer id="drawer-create-navigation">
        <x-weblebby::drawer.header :title="__('Yeni menü')" />
        <x-weblebby::form class="fd-space-y-3" :action="panel_route('navigations.store')">
            <x-weblebby::form.group name="title">
                <x-weblebby::form.label>@lang('Başlık')</x-weblebby::form.label>
                <x-weblebby::form.input
                        :placeholder="__('örn. Ana menü')"
                        data-drawer-focus
                />
            </x-weblebby::form.group>
            <x-weblebby::form.group name="handle">
                <x-weblebby::form.label>@lang('Tanımlayıcı')</x-weblebby::form.label>
                <x-weblebby::form.input :placeholder="__('örn. ana-menu')" />
            </x-weblebby::form.group>
            <x-weblebby::button type="submit">@lang('Oluştur')</x-weblebby::button>
        </x-weblebby::form>
    </x-weblebby::drawer>

    @push('after_scripts')
        @vite('resources/js/navigation.js', panel_build_path())
        <script>
            @if ($errors->item->any())
            document.addEventListener("DOMContentLoaded", function() {
              Weblebby.Drawer.open(document.getElementById("drawer-create-menu-item"), {
                hasError: true,
                item: @json(old()) });
            });
            @endif

            @if ($selectedNavigation ?? null)
            document.addEventListener("DOMContentLoaded", function() {
              Weblebby.Navigation.init({{ $selectedNavigation->id }});
            });
            @endif
        </script>
    @endpush
</x-weblebby::layouts.panel>
