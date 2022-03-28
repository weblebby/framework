@props(['checkbox' => false])

<td {{ $attributes->class('fd-px-4 fd-py-3 fd-whitespace-nowrap')->class($checkbox ? 'fd-w-1' : '') }}>
    <div class="fd-flex fd-items-center fd-text-zinc-600">{{ $slot }}</div>
</td>