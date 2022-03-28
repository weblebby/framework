@props(['as' => 'div', 'padding' => false])

<{{ $as }} {{ $attributes
    ->class('fd-bg-white fd-shadow fd-rounded')
    ->class($padding ? 'fd-p-4' : '') }}>
    {{ $slot }}
</{{ $as }}>