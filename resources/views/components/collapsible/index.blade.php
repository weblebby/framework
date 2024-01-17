@props(['expanded' => false])

<div data-collapsible aria-expanded="{{ $expanded ? 'true' : 'false' }}" {{ $attributes }}>
    {{ $slot }}
</div>
