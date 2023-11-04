@props(['extension'])

<?php /** @var \Feadmin\Items\ExtensionItem $extension */ ?>

<x-feadmin::card padding>
    <div class="fd-flex fd-items-center fd-gap-2 fd-text-zinc-700">
        <h3 class="fd-font-medium">{{ $extension->pluralTitle() }}</h3>
    </div>
    <div class="fd-text-zinc-600 fd-text-sm fd-mt-1">{{ $extension->description() }}</div>
    <x-feadmin::form
            :action="panel_route('extensions.' . ($extension->isActive() ? 'disable' : 'enable'), $extension->name())"
            method="PUT" class="fd-flex fd-items-center fd-justify-between fd-gap-2 fd-mt-3">
        @if ($extension->isActive())
            @can('extension:update')
                <x-feadmin::button icon="check" variant="green" size="sm">@lang('Etkin')</x-feadmin::button>
            @endcan
            @if (panel()->preference($extension->name())->get())
                <x-feadmin::button
                        as="a"
                        :href="panel_route('extensions.preferences.index', $extension->name())"
                        icon="gear-fill"
                />
            @endif
        @else
            @can('extension:update')
                <x-feadmin::button size="sm">@lang('Etkinleştir')</x-feadmin::button>
            @endcan
        @endif
    </x-feadmin::form>
</x-feadmin::card>
