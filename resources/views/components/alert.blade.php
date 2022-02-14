@props([
    'color' => null,
    'dismissible' => false,
])

@php
    $colorClasses = match($color) {
        'red' => 'bg-red-100 text-red-500 shadow-red-200',
        'green' => 'bg-green-100 text-green-500 shadow-green-200',
        default => 'bg-zinc-100',
    }
@endphp

<div {{ $attributes->class('p-4 rounded font-medium shadow')->class($colorClasses) }}>{{ $slot }}</div>