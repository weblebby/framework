@aware(['name', 'bag' => 'default'])

@php($name = FormComponent::dottedName($name))

@if ($name && $errors->{$bag}->has($name))
    <span class="fd-text-xs fd-text-red-500">{{ $errors->{$bag}->first($name) }}</span>
@endif