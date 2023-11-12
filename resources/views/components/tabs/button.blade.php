@props(['id'])
@aware(['default'])

@php($id = sprintf('tab-%s', $id))
@php($default = sprintf('tab-%s', $default ?? 'none'))

<button {{ $attributes
    ->class([
        'fd-py-2 fd-px-4',
        'fd-bg-zinc-100 fd-font-medium' => $id === $default,
    ])
    ->merge([
        'data-tab-button' => $id,
        'type' => 'button'
    ])
}}>{{ $slot }}</button>
