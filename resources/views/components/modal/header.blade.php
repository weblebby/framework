@props(['title', 'subtitle'])

<div class="fd-flex fd-items-center fd-justify-between fd-gap-2 fd-py-3 fd-px-4">
    <div>
        <div class="fd-text-xl fd-font-bold">{{ $title }}</div>
        @if ($subtitle ?? null)
            <div class="fd-text-zinc-500 fd-font-medium">{{ $subtitle }}</div>
        @endif
    </div>
    <x-weblebby::button icon="x" data-modal-close />
</div>