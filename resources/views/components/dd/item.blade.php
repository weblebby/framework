@props(['item', 'readonly' => false])

<li class="dd-item" data-id="{{ $item->id }}">
    <div class="navigation-item px-4 py-3 border-y -mt-[1px] bg-white group">
        <div class="flex items-center justify-between">
            <span>{{ $item->title }}</span>
            <div class="flex items-center gap-1 dd-nodrag opacity-0 group-hover:opacity-100 transition-opacity">
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