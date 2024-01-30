@props(['default', 'label', 'deleteable' => true])

<div class="fd-flex fd-items-center fd-justify-between fd-gap-2 fd-bg-white fd-shadow fd-rounded-lg fd-p-2"
     data-file-info>
    <a href="{{ $default }}" target="_blank"
       class="fd-flex fd-items-center fd-gap-2 fd-text-sm fd-text-sky-600 hover:fd-text-sky-700">
        {{ $label ?? $default }}
        <x-weblebby::icons.box-arrow-up-right class="fd-w-4 fd-h-4" />
    </a>
    @if ($deleteable)
        <x-weblebby::button type="button" icon="x" variant="red" size="none" class="fd-p-1" data-delete-file />
    @endif
</div>