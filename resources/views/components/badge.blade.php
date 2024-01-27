@props(['variant' => 'primary'])

<div {{ $attributes->class([
    'fd-rounded-full fd-text-xs fd-uppercase fd-font-medium fd-px-2 fd-py-0.5',
    'fd-bg-zinc-800 fd-text-white' => $variant === 'primary',
    'fd-bg-green-700 fd-text-white' => $variant === 'success',
    'fd-bg-zinc-200 fd-text-zinc-700' => $variant === 'light',
    ]) }}>
    {{ $slot }}
</div>
