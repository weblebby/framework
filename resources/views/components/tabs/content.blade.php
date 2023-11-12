@props(['for'])
@aware(['default'])

@php($id = sprintf('tab-%s', $for))
@php($default = sprintf('tab-%s', $default ?? 'none'))

<div {{ $attributes
    ->class('fd-p-3 fd-rounded-b')
    ->merge([
        'data-tab-content' => $id,
        ...$id === $default ? [] : ['style' => 'display: none;'],
    ])
}}>
    {{ $slot ?? null }}
</div>
