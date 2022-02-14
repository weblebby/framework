@props(['checkbox' => false])

<td {{ $attributes->class('px-4 py-3 whitespace-nowrap')->class($checkbox ? 'w-1' : '') }}>
    <div class="flex items-center text-zinc-600">{{ $slot }}</div>
</td>