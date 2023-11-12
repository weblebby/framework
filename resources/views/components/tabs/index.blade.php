@props(['default', 'container'])

<div {{ $attributes->class('fd-bg-white fd-rounded')->merge(['data-tab-container' => $container]) }}>
    {{ $slot }}
</div>
