@props([
    'color' => null,
    'type' => null,
    'dismissible' => false,
    'subtitle',
    'icon',
])

@php
    $colorClasses = match($color) {
        'red' => 'fd-bg-red-100 fd-text-red-500 fd-shadow-red-200 fd-border-red-300',
        'green' => 'fd-bg-green-100 fd-text-green-500 fd-shadow-green-200 fd-border-green-300',
        'orange' => 'fd-bg-orange-100 fd-text-orange-500 fd-shadow-orange-200 fd-border-orange-300',
        default => 'fd-bg-zinc-100 fd-text-zinc-600 fd-shadow-zinc-200 fd-border-zinc-300',
    };

    $typeClasses = match($type) {
        'minimal' => 'fd-px-3 fd-py-2 fd-border-l-2',
        default => 'fd-shadow fd-p-4',
    };
@endphp

<div {{ $attributes
    ->class('fd-flex fd-items-center fd-gap-3 fd-rounded fd-font-medium')
    ->class($colorClasses)
    ->class($typeClasses) }}>
    @if ($icon ?? null)
        <x-dynamic-component component="feadmin::icons.{{ $icon }}" class="fd-w-5 fd-h-5" />
    @endif
    <div>
        <div>{{ $slot }}</div>
        @if ($subtitle ?? false && $subtitle->isNotEmpty())
            <div class="fd-text-sm fd-font-normal">{{ $subtitle }}</div>
        @endif
    </div>
</div>