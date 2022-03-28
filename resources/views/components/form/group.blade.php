@props(['name' => null, 'bag' => 'default'])

<div {{ $attributes
    ->merge(['data-form-group' => $name])
    ->class('fd-flex fd-flex-col fd-space-y-1') }}>
    {{ $slot }}
    <x-feadmin::form.errors />
</div>