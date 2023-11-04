<?php /** @var \Feadmin\Items\ExtensionItem $extension */ ?>

<x-feadmin::layouts.panel>
    <x-feadmin::page class="{{ count($preferences) <= 1 ? 'lg:fd-w-2/3 fd-mx-auto' : '' }}">
        <x-feadmin::page.head :back="panel_route('extensions.index')">
            <x-feadmin::page.title>{{ $extension->pluralTitle() }}</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang(':extension modülünü ayarlayın', ['extension' => $extension->singularTitle()])</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <div>
            <div class="{{ count($preferences) > 1 ? 'fd-grid lg:fd-grid-cols-5 fd-gap-3' : '' }}">
                @if(count($preferences) > 1)
                    <div>
                        <x-feadmin::link-card>
                            @foreach ($preferences as $bag => $preference)
                                <x-feadmin::link-card.item
                                        href="{{ panel_route('extensions.preferences.show', [$extension->name(), $bag]) }}"
                                        :active="$selectedBag === $bag"
                                >
                                    {{ $preference['title'] }}
                                </x-feadmin::link-card.item>
                            @endforeach
                        </x-feadmin::link-card>
                    </div>
                @endif
                <div class="lg:fd-col-span-4">
                    <x-feadmin::card padding>
                        <x-feadmin::form
                                :action="panel_route('extensions.preferences.update', [$extension->name(), $selectedBag])"
                                method="PUT"
                                enctype="multipart/form-data"
                        >
                            <div class="fd-space-y-3">
                                @foreach (panel()->preference($extension->name())->fields($selectedBag) as $field)
                                    <x-feadmin::form.field :field="$field"/>
                                @endforeach
                                <x-feadmin::button type="submit">@lang('Kaydet')</x-feadmin::button>
                            </div>
                        </x-feadmin::form>
                    </x-feadmin::card>
                </div>
            </div>
        </div>
    </x-feadmin::page>
</x-feadmin::layouts.panel>
