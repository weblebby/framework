@props(['extension'])

<?php /** @var \Weblebby\Framework\Abstracts\Extension\Extension $extension */ ?>

<x-weblebby::card padding>
    <div class="fd-flex fd-flex-col fd-gap-3 fd-h-full">
        <div class="space-y-1">
            <div class="fd-flex fd-items-center fd-gap-1.5 fd-text-zinc-700">
                @if ($extension->isActive())
                    <div class="fd-w-3 fd-h-3 fd-bg-green-600 fd-rounded-full"></div>
                @endif
                <h2 class="fd-font-medium">{{ $extension->singularTitle() }}</h2>
            </div>
            <div class="fd-text-zinc-500 fd-text-sm fd-mt-1 fd-line-clamp-3">{{ $extension->description() }}</div>
        </div>
        <x-weblebby::form
                :action="panel_route('extensions.' . ($extension->isActive() ? 'disable' : 'enable'), $extension->name())"
                method="PUT"
                class="fd-flex-1 fd-flex fd-items-end"
        >
            <div class="fd-flex fd-items-center fd-justify-between fd-gap-2 fd-border-t fd-w-full fd-pt-3">
                @if ($extension->isActive())
                    @can('extension:deactivate')
                        <x-weblebby::button size="sm" variant="red">@lang('Deactivate')</x-weblebby::button>
                    @endcan
                    @if (panel()->preference($extension->name())->get())
                        <x-weblebby::button
                                as="a"
                                :href="panel_route('extensions.preferences.index', $extension->name())"
                                icon="gear-fill"
                                variant="light"
                        />
                    @endif
                @else
                    @can('extension:activate')
                        <x-weblebby::button size="sm">@lang('Activate')</x-weblebby::button>
                    @endcan
                @endif
            </div>
        </x-weblebby::form>
    </div>

</x-weblebby::card>
