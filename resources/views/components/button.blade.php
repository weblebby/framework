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
        'light' => 'bg-zinc-100 text-zinc-500 border border-zinc-300 hover:bg-zinc-50 hover:text-zinc-700',
        'outline-light' => 'text-zinc-500 border border-zinc-300 hover:bg-zinc-100 hover:border-transparent',
        'green' => 'bg-green-100 text-green-500 border border-green-200 hover:bg-green-200 hover:text-green-700 hover:border-green-300',
        'sky' => 'bg-sky-100 text-sky-500 border border-sky-200 hover:bg-sky-200 hover:text-sky-700 hover:border-sky-300',
        'red' => 'bg-red-100 text-red-500 border border-red-200 hover:bg-red-200 hover:text-red-700 hover:border-red-300',
        'link' => 'text-zinc-500 hover:bg-zinc-100',
        default => 'bg-zinc-800 text-zinc-100 border border-zinc-900 hover:bg-zinc-700 hover:text-zinc-200 hover:border-zinc-800',
    };

    $sizeClasses = match($size) {
        'icon' => 'p-2',
        'sm' => 'px-3 py-1',
        'lg' => 'px-5 py-3',
        'none' => '',
        default => 'px-4 py-2',
    };

    $roundedClasses = match($rounded) {
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
        'none' => '',
        default => 'rounded',
    };
@endphp

<{{ $as }} {{ $attributes
    ->class('flex items-center gap-2 transition-colors')
    ->class($upper ? 'uppercase text-sm font-medium' : '')
    ->class($roundedClasses)
    ->class($sizeClasses)
    ->class($variantClasses) }}>
    @if ($icon ?? null)
        <x-dynamic-component component="feadmin::icons.{{ $icon }}" class="w-4 h-4" />
    @endif
    <div data-spinner>
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
    {{ $slot }}
</{{ $as }}>