@props([
    'color' => null,
    'type' => null,
    'dismissible' => false,
    'subtitle',
])

@php
    $colorClasses = match($color) {
        'red' => 'bg-red-100 text-red-500 shadow-red-200 border-red-300',
        'green' => 'bg-green-100 text-green-500 shadow-green-200 border-green-300',
        'orange' => 'bg-orange-100 text-orange-500 shadow-orange-200 border-orange-300',
        default => 'bg-zinc-100 text-zinc-600 shadow-zinc-200 border-zinc-300',
    };

    $typeClasses = match($type) {
        'minimal' => 'px-3 py-2 border-l-2',
        default => 'shadow p-4',
    };
@endphp

<div {{ $attributes
    ->class('rounded font-medium')
    ->class($colorClasses)
    ->class($typeClasses) }}>
    <div>{{ $slot }}</div>
    @if ($subtitle ?? false && $subtitle->isNotEmpty())
        <div class="text-sm font-normal">{{ $subtitle }}</div>
    @endif
</div>