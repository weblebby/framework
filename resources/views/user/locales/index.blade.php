<x-feadmin::layouts.panel>
    <x-slot name="scripts">
        @if ($errors->any())
            <script>
              Feadmin.Drawer.open(document.getElementById("drawer-create-locale"));
            </script>
        @endif
    </x-slot>
    <x-feadmin::page>
        <div>
            @if ($selectedLocale ?? null)
                <x-feadmin::page.title>{{ $localeName = Localization::display($selectedLocale->code) }}</x-feadmin::page.title>
                <x-feadmin::page.subtitle>@lang('Çevirileri düzenleyin')</x-feadmin::page.subtitle>
            @else
                <x-feadmin::page.title>@lang('Diller')</x-feadmin::page.title>
                <x-feadmin::page.subtitle>@lang('Sitenizin dillerini yönetin')</x-feadmin::page.subtitle>
            @endif
        </div>
        <div>
            <div class="fd-grid fd-grid-cols-5 fd-gap-3">
                <div class="fd-space-y-3">
                    @if ($availableLocales->isNotEmpty())
                        <x-feadmin::link-card>
                            @foreach ($availableLocales as $locale)
                                <x-feadmin::link-card.item
                                        class="fd-justify-between"
                                        href="{{ panel_route('locales.show', $locale->id) }}"
                                        :active="$locale->id === ($selectedLocale->id ?? null)">
                                    {{ Localization::display($locale->code) }}
                                    @if ($locale->is_default)
                                        <x-feadmin::badge>@lang('Varsayılan')</x-feadmin::badge>
                                    @endif
                                </x-feadmin::link-card.item>
                            @endforeach
                        </x-feadmin::link-card>
                    @endif
                    @can('locale:create')
                        <x-feadmin::link-card>
                            <x-feadmin::link-card.item
                                    as="button"
                                    icon="plus"
                                    data-drawer="#drawer-create-locale"
                            >@lang('Yeni dil')</x-feadmin::link-card.item>
                        </x-feadmin::link-card>
                    @endcan
                </div>
                <div class="fd-col-span-4">
                    @if ($selectedLocale ?? null)
                        <div class="fd-flex fd-items-center fd-gap-2 fd-mb-3">
                            <div class="fd-flex fd-gap-3">
                                @can('locale:translate')
                                    <x-feadmin::form :action="panel_route('locales.sync')">
                                        <x-feadmin::button
                                                type="submit"
                                                variant="light"
                                                icon="arrow-clockwise"
                                                upper
                                        >@lang('Çevirileri senkronize et')</x-feadmin::button>
                                    </x-feadmin::form>
                                @endcan
                                @can('locale:delete')
                                    <x-feadmin::button
                                            data-modal-open="#modal-delete-locale"
                                            variant="red"
                                            upper
                                    >@lang('Dili sil')</x-feadmin::button>
                                @endcan
                            </div>
                        </div>
                        <form class="fd-flex fd-items-center fd-gap-2 fd-mb-3">
                            <x-feadmin::form.group name="search" class="fd-flex-[3]">
                                <x-feadmin::form.input :placeholder="__('Çevirilerde ara')" />
                            </x-feadmin::form.group>
                            <x-feadmin::form.select name="status" onchange="this.form.submit()" class="fd-flex-1">
                                <x-feadmin::form.option value="">@lang('Tüm çeviriler')</x-feadmin::form.option>
                                @foreach (\Feadmin\Enums\TranslationStatusEnum::cases() as $case)
                                    <x-feadmin::form.option
                                            value="{{ $case->value }}">{{ $case->label() }}</x-feadmin::form.option>
                                @endforeach
                            </x-feadmin::form.select>
                        </form>
                        <x-feadmin::card class="fd-divide-y">
                            <div class="fd-py-4 fd-space-y-3">
                                @forelse ($translations as $key => $value)
                                    <div class="fd-grid fd-grid-cols-2 fd-divide-x">
                                        <div class="fd-px-4">
                                            <div class="fd-relative">
                                                <x-feadmin::form.input
                                                        :default="__($key, locale: Localization::getDefaultLocaleCode())"
                                                        readonly
                                                />
                                                <div class="fd-absolute fd-right-4 fd-top-1/2 -fd-translate-y-1/2">
                                                    <x-feadmin::badge>{{ Localization::getDefaultLocaleCode() }}</x-feadmin::badge>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="fd-px-4">
                                            <div class="fd-relative">
                                                <x-feadmin::form.input
                                                        :default="__($key, locale: $selectedLocale->code)"
                                                        :data-key="$key"
                                                        :data-code="$selectedLocale->code"
                                                        data-translation-input
                                                        tabindex="1"
                                                        :readonly="auth()->user()->cannot('locale:translate')"
                                                />
                                                <div class="fd-absolute fd-right-4 fd-top-1/2 -fd-translate-y-1/2">
                                                    <x-feadmin::badge>{{ $selectedLocale->code }}</x-feadmin::badge>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="fd-px-4">
                                        @if (request()->filled('search') || request()->filled('status'))
                                            <p class="fd-text-zinc-600">@lang('Arama sonuçlarıyla eşleşen bir sonuç yok.')</p>
                                        @else
                                            <p class="fd-text-zinc-600">@lang('Çeviriler senkronize edilmemiş, yukarıdaki butona tıklayıp senkronizasyon işlemini başlatabilirsiniz.')</p>
                                        @endif
                                    </div>
                                @endforelse
                            </div>
                        </x-feadmin::card>
                        <div class="fd-mt-3">
                            <x-feadmin::overflow>{{ $translations->withQueryString()->links() }}</x-feadmin::overflow>
                        </div>
                    @else
                        <x-feadmin::empty
                                icon="translate"
                                :title="__('Dil seçin')"
                                :content="__('Yönetmek istediğiniz dili seçin')"
                        />
                    @endif
                </div>
            </div>
        </div>
    </x-feadmin::page>
    @can('locale:create')
        <x-feadmin::drawer id="drawer-create-locale">
            <x-feadmin::drawer.header :title="__('Yeni dil')" />
            <x-feadmin::form class="fd-space-y-3" :action="panel_route('locales.store')">
                <x-feadmin::form.group name="code">
                    <x-feadmin::form.label>@lang('Dil')</x-feadmin::form.label>
                    <x-feadmin::form.select data-drawer-focus>
                        <option selected disabled>@lang('Dil seçin')</option>
                        @foreach ($remainingLocales as $code => $locale)
                            <x-feadmin::form.option value="{{ $code }}">{{ $locale }}</x-feadmin::form.option>
                        @endforeach
                    </x-feadmin::form.select>
                </x-feadmin::form.group>
                <x-feadmin::form.group name="is_default">
                    <x-feadmin::form.checkbox :label="__('Varsayılan dil')" />
                </x-feadmin::form.group>
                <x-feadmin::button type="submit">@lang('Oluştur')</x-feadmin::button>
            </x-feadmin::form>
        </x-feadmin::drawer>
    @endcan
    @can('locale:delete')
        @if ($selectedLocale ?? null)
            <x-feadmin::modal.destroy
                    id="modal-delete-locale"
                    :action="panel_route('locales.destroy', $selectedLocale->id)"
                    :title="__(':locale dilini siliyorsunuz', ['locale' => $localeName])"
                    :subtitle="__('Bu dili ve ilişkili tüm çevirileri silmek istediğinize emin misiniz?')"
            />
        @endif
    @endcan
</x-feadmin::layouts.panel>
