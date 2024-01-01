<x-feadmin::layouts.panel>
    <x-feadmin::page>
        <div>
            <x-feadmin::page.title>@lang('Ayarlar')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang('Sitenizin tüm ayarlarını buradan yönetin')</x-feadmin::page.subtitle>
        </div>
        <div>
            <div class="fd-grid fd-grid-cols-5 fd-gap-3">
                <div>
                    <x-feadmin::link-card>
                        @foreach ($bags as $id => $bag)
                            <x-feadmin::link-card.item
                                    href="{{ panel_route('preferences.show', $id) }}"
                                    :active="$selectedBagId === $id">
                                {{ $bag['title'] }}
                            </x-feadmin::link-card.item>
                        @endforeach
                    </x-feadmin::link-card>
                </div>
                <div class="fd-col-span-4">
                    <x-feadmin::card padding>
                        <x-feadmin::form :action="panel_route('preferences.update', [$namespace, $selectedBagId])"
                                         method="PUT" enctype="multipart/form-data">
                            <div class="fd-space-y-3">
                                @foreach ($fields as $field)
                                    <x-feadmin::form.field :field="$field" />
                                @endforeach
                                <x-feadmin::button type="submit">@lang('Kaydet')</x-feadmin::button>
                            </div>
                        </x-feadmin::form>
                    </x-feadmin::card>
                </div>
            </div>
        </div>
    </x-feadmin::page>
    @if($isCodeEditorNeeded)
        <x-slot:scripts>
            @vite('resources/js/code-editor.js', 'feadmin')
        </x-slot:scripts>
    @endif
</x-feadmin::layouts.panel>
