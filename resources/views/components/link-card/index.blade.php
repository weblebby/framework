@props(['title'])

<div {{ $attributes->class('fd-flex fd-flex-col fd-bg-white fd-shadow fd-rounded fd-overflow-hidden') }}>
    @if ($title ?? null)
        <div class="fd-py-2 fd-px-4 fd-font-medium">{{ $title }}</div>
    @endif
    <div class="fd-divide-y">{{ $slot }}</div>
</div>