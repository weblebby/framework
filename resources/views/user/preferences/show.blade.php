<x-weblebby::layouts.panel>
    <x-weblebby::page>
        <x-weblebby::page.head>
            <x-weblebby::page.title>{{ $pageTitle }}</x-weblebby::page.title>
            <x-weblebby::page.subtitle>{{ $pageDescription }}</x-weblebby::page.subtitle>
        </x-weblebby::page.head>
        <div>
            <div class="fd-grid fd-grid-cols-5 fd-gap-3">
                <div>
                    <x-weblebby::link-card>
                        @foreach ($bags as $id => $bag)
                            <x-weblebby::link-card.item
                                    href="{{ panel_route($route, [...$routeParams, $id]) }}"
                                    :active="$selectedBagId === $id">
                                {{ $bag['title'] }}
                            </x-weblebby::link-card.item>
                        @endforeach
                    </x-weblebby::link-card>
                </div>
                <div class="fd-col-span-4">
                    <x-weblebby::card padding>
                        <x-weblebby::form :action="panel_route('preferences.update', [$namespace, $selectedBagId])"
                                          method="PUT" enctype="multipart/form-data">
                            @hook(panel()->nameWith('preference:before_form_fields'))
                            <div class="fd-space-y-3">
                                @foreach ($fields as $field)
                                    <x-weblebby::form.field :field="$field" />
                                @endforeach
                                <x-weblebby::button type="submit">@lang('Kaydet')</x-weblebby::button>
                            </div>
                        </x-weblebby::form>
                    </x-weblebby::card>
                </div>
            </div>
        </div>
    </x-weblebby::page>
    @if($isCodeEditorNeeded)
        @push('after_scripts')
            @vite('resources/js/code-editor.js', panel_build_path())
        @endpush
    @endif
</x-weblebby::layouts.panel>
