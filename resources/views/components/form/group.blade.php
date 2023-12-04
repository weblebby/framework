@props(['name' => null, 'bag' => 'default', 'withErrors' => true])

@php($dottedName = \Feadmin\Support\FormComponent::nameToDottedWithoutEmptyWildcard($name))

<div {{ $attributes
    ->merge(['data-form-group' => $dottedName, 'data-original-form-group' => $dottedName])
    ->class('fd-flex fd-flex-col fd-space-y-1')
    ->class($dottedName && $errors->{$bag}->has($dottedName) && $withErrors ? 'fd-has-error' : '') }}>
    {{ $slot }}
    @if ($withErrors)
        <x-feadmin::form.errors />
    @endif
</div>