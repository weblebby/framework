@props([
    'name' => null,
    'bag' =>
    'default',
    'withErrors' => true,
    'label' => null,
    'attributes' => $attributes,
    'hidden' => false,
])

@php($dottedName = \Feadmin\Support\FormComponent::nameToDottedWithoutEmptyWildcard($name))

<div {{ $attributes
    ->merge(['data-form-group' => $dottedName, 'data-original-form-group' => $dottedName, 'hidden' => $hidden])
    ->class(['fd-flex fd-flex-col fd-space-y-1', 'fd-hidden' => $hidden])
    ->class($dottedName && $errors->{$bag}->has($dottedName) && $withErrors ? 'fd-has-error' : '') }}>
    @if ($label)
        <x-feadmin::form.label>{{ $label }}</x-feadmin::form.label>
    @endif
    {{ $slot }}
    @if ($withErrors)
        <x-feadmin::form.errors />
    @endif
</div>