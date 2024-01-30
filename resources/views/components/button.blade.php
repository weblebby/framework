@props([
    'as' => 'button',
    'variant' => 'black',
    'size' => null,
    'rounded' => null,
    'upper' => false,
    'icon',
])

@php
    if ($slot->isEmpty() && isset($icon)) {
        $size ??= 'icon';
        $rounded ??= 'full';
    }

    $variantClasses = match($variant) {
        'light' => 'fd-bg-zinc-100 fd-text-zinc-500 fd-border fd-border-zinc-300 hover:fd-bg-zinc-50 hover:fd-text-zinc-700',
        'outline-light' => 'fd-text-zinc-500 fd-border fd-border-zinc-300 hover:fd-bg-zinc-100 hover:fd-border-transparent',
        'green' => 'fd-bg-green-100 fd-text-green-500 fd-border fd-border-green-200 hover:fd-bg-green-200 hover:fd-text-green-700 hover:fd-border-green-300',
        'sky' => 'fd-bg-sky-100 fd-text-sky-500 fd-border fd-border-sky-200 hover:fd-bg-sky-200 hover:fd-text-sky-700 hover:fd-border-sky-300',
        'red' => 'fd-bg-red-100 fd-text-red-500 fd-border fd-border-red-200 hover:fd-bg-red-200 hover:fd-text-red-700 hover:fd-border-red-300',
        'link' => 'fd-text-zinc-500 hover:fd-bg-zinc-100',
        default => 'fd-bg-zinc-800 fd-text-zinc-100 fd-border fd-border-zinc-900 hover:fd-bg-zinc-700 hover:fd-text-zinc-200 hover:fd-border-zinc-800',
    };

    $sizeClasses = match($size) {
        'icon' => 'fd-p-2',
        'sm' => 'fd-px-3 fd-py-1',
        'lg' => 'fd-px-5 fd-py-3',
        'full' => 'fd-px-4 fd-py-2 fd-w-full fd-justify-center',
        'none' => '',
        default => 'fd-px-4 fd-py-2',
    };

    $roundedClasses = match($rounded) {
        'sm' => 'fd-rounded-sm',
        'md' => 'fd-rounded-md',
        'lg' => 'fd-rounded-lg',
        'full' => 'fd-rounded-full',
        'none' => '',
        default => 'fd-rounded',
    };
@endphp

<{{ $as }} {{ $attributes
    ->class('fd-flex fd-items-center fd-gap-2 fd-transition-colors')
    ->class($upper ? 'fd-uppercase fd-text-sm fd-font-medium' : '')
    ->class($roundedClasses)
    ->class($sizeClasses)
    ->class($variantClasses) }}>
@if ($icon ?? null)
    <x-dynamic-component component="weblebby::icons.{{ $icon }}" class="fd-w-4 fd-h-4" />
@endif
<div data-spinner>
    <svg class="fd-animate-spin fd-h-5 fd-w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="fd-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="fd-opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</div>
{{ $slot }}
</{{ $as }}>