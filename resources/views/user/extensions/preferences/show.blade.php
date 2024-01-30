<?php /** @var \Weblebby\Framework\Items\ExtensionItem $extension */ ?>

<x-weblebby::layouts.panel>
    <x-weblebby::page class="{{ count($preferences) <= 1 ? 'lg:fd-w-2/3 fd-mx-auto' : '' }}">
        <x-weblebby::page.head :back="panel_route('extensions.index')">
            <x-weblebby::page.title>{{ $extension->pluralTitle() }}</x-weblebby::page.title>
            <x-weblebby::page.subtitle>@lang(':extension modülünü ayarlayın', ['extension' => $extension->singularTitle()])</x-weblebby::page.subtitle>
        </x-weblebby::page.head>
        <div>
            <div class="{{ count($preferences) > 1 ? 'fd-grid lg:fd-grid-cols-5 fd-gap-3' : '' }}">
                @if(count($preferences) > 1)
                    <div>
                        <x-weblebby::link-card>
                            @foreach ($preferences as $bag => $preference)
                                <x-weblebby::link-card.item
                                        href="{{ panel_route('extensions.preferences.show', [$extension->name(), $bag]) }}"
                                        :active="$selectedBag === $bag"
                                >
                                    {{ $preference['title'] }}
                                </x-weblebby::link-card.item>
                            @endforeach
                        </x-weblebby::link-card>
                    </div>
                @endif
                <div class="lg:fd-col-span-4">
                    <x-weblebby::card padding>
                        <x-weblebby::form
                                :action="panel_route('extensions.preferences.update', [$extension->name(), $selectedBag])"
                                method="PUT"
                                enctype="multipart/form-data"
                        >
                            <div class="fd-space-y-3">
                                @foreach (panel()->preference($extension->name())->fields($selectedBag) as $field)
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
</x-weblebby::layouts.panel>
