@props(['active' => false])

<a {{ $attributes
    ->class('fd-px-4 fd-py-1.5 fd-border-l-4 fd-text-zinc-600 fd-text-sm fd-transition-colors')
    ->class($active ? 'fd-font-bold fd-border-zinc-600 fd-bg-zinc-300' : 'fd-border-transparent hover:fd-bg-zinc-300') }}>{{ $slot }}</a>