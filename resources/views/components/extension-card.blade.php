@props(['extension'])

<x-feadmin::card padding>
    <div class="flex items-center gap-2 text-zinc-700">
        <h3 class="font-medium">{{ $extension->plural_title }}</h3>
    </div>
    <div class="text-zinc-600 text-sm mt-1">{{ $extension->description }}</div>
    <x-feadmin::form :action="route('admin::extensions.' . ($extension->is_enabled ? 'disable' : 'enable'), $extension->id)" method="PUT" class="flex items-center justify-between gap-2 mt-3">
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