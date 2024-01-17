@props(['suffix' => false])

<div class="fd-absolute fd-top-2 fd-bottom-0 {{ $suffix ? '-fd-right-0 fd-translate-x-full' : '-fd-left-0 -fd-translate-x-full' }}">
    <div {{ $attributes->class('fd-bg-white fd-border fd-border-gray-300 fd-p-1 fd-text-sm fd-font-medium fd-sticky fd-top-2')->merge(['data-form-prefix' => true]) }}>{{ $slot }}</div>
</div>
