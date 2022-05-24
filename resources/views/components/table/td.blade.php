@props(['checkbox' => false])

<td {{ $attributes->class('fd-text-zinc-600 fd-px-4 fd-py-3')->class($checkbox ? 'fd-w-1' : '') }}>
    {{ $slot }}
</td>