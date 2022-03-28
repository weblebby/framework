@props(['item', 'readonly' => false])

<li class="dd-item" data-id="{{ $item->id }}">
    <div class="navigation-item fd-px-4 fd-py-3 fd-border-y -fd-mt-[1px] fd-bg-white fd-group">
        <div class="fd-flex fd-items-center fd-justify-between">
            <span>{{ $item->title }}</span>
            <div class="dd-nodrag fd-flex fd-items-center fd-gap-1 fd-opacity-0 group-hover:fd-opacity-100 fd-transition-opacity">
                @if ($readonly !== true)
                    <x-feadmin::button
                        icon="pencil-fill"
                        variant="light"
                        data-toggle="edit"
                        :data-item="json_encode($item->toExport())"
                    />
                    <x-feadmin::button
                        icon="x"
                        variant="red"
                        data-modal-open="#modal-delete-item"
                        :data-action="route('admin::navigations.items.destroy', [$item->navigation_id, $item->id])"
                    />
                @endif
            </div>
        </div>
    </div>
    {{ $slot }}
</li>