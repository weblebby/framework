@props(['name', 'bag' => 'default'])

<div {{ $attributes->merge(['data-form-group' => $name])->class('flex flex-col space-y-1') }}>
    {{ $slot }}
    <x-feadmin::form.errors />
</div>