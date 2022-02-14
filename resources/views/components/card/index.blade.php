@props(['as' => 'div', 'padding' => false])

<{{ $as }} {{ $attributes
    ->class('bg-white shadow rounded')
    ->class($padding ? 'p-4' : '') }}>
    {{ $slot }}
</{{ $as }}>