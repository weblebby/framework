@props(['mb' => false])

<div {{ $attributes->class('fd-text-zinc-600 fd-text-xs')->class($mb ? '!fd-mb-1' : '') }}>{{ $slot }}</div>