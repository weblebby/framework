@props([
    'color' => null,
    'type' => null,
    'dismissible' => false,
    'subtitle',
    'icon',
])

@php
    $typeClasses = match($type) {
        'classic' => 'fd-shadow fd-p-4 fd-rounded',
        'header' => 'fd-p-3',
        default => 'fd-px-3 fd-py-2 fd-border-l-2 fd-rounded',
    };

    $colorClasses = match($color) {
        'red' => match($type) {
            'header' => 'fd-bg-red-500 fd-text-white',
            default => 'fd-bg-red-100 fd-text-red-500 fd-shadow-red-200 fd-border-red-300',
        },
        'green' => 'fd-bg-green-100 fd-text-green-500 fd-shadow-green-200 fd-border-green-300',
        'orange' => match($type) {
            'header' => 'fd-bg-orange-500 fd-text-white',
            default => 'fd-bg-orange-100 fd-text-orange-500 fd-shadow-orange-200 fd-border-orange-300',
        },
        default => 'fd-bg-zinc-100 fd-text-zinc-600 fd-shadow-zinc-200 fd-border-zinc-300',
    };
@endphp

<div {{ $attributes
    ->class('fd-flex fd-items-center fd-gap-3 fd-font-medium')
    ->class($colorClasses)
    ->class($typeClasses) }}>
    @if ($icon ?? null)
        <x-dynamic-component component="feadmin::icons.{{ $icon }}" class="fd-w-5 fd-h-5" />
    @endif
    <div class="fd-flex-1">
        <div>{{ $slot }}</div>
        @if ($subtitle ?? false && $subtitle->isNotEmpty())
            <div class="fd-text-sm fd-font-normal">{{ $subtitle }}</div>
        @endif
    </div>
</div>