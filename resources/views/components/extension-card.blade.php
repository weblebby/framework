@props(['extension'])

<x-feadmin::card padding>
    <div class="fd-flex fd-items-center fd-gap-2 fd-text-zinc-700">
        <h3 class="fd-font-medium">{{ $extension->plural_title }}</h3>
    </div>
    <div class="fd-text-zinc-600 fd-text-sm fd-mt-1">{{ $extension->description }}</div>
    <x-feadmin::form :action="route('admin::extensions.' . ($extension->is_enabled ? 'disable' : 'enable'), $extension->id)" method="PUT" class="fd-flex fd-items-center fd-justify-between fd-gap-2 fd-mt-3">
        @if ($extension->is_enabled)
            @can('extension:update')
                <x-feadmin::button icon="check" variant="green" size="sm">@t('Etkin', 'panel')</x-feadmin::button>
            @endcan
            @if (PreferenceManager::namespaces($extension->id))
                <x-feadmin::button as="a" :href="route('admin::extensions.preferences.index', $extension->id)" icon="gear-fill" />
            @endif
        @else
            @can('extension:update')
                <x-feadmin::button size="sm">@t('Etkinleştir', 'panel')</x-feadmin::button>
            @endcan
        @endif
    </x-feadmin::form>
</x-feadmin::card>