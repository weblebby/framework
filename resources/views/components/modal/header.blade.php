@props(['title', 'subtitle'])

<div class="flex items-center justify-between gap-2 py-3 px-4">
    <div>
        <div class="text-xl font-bold">{{ $title }}</div>
        @if ($subtitle ?? null)
            <div class="text-zinc-500 font-medium">{{ $subtitle }}</div>
        @endif
    </div>
    <x-feadmin::button icon="x" data-modal-close />
</div>