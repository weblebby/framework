@aware(['name', 'bag' => 'default'])

@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($name))

@if ($name && $errors->{$bag}->has($dottedName))
    <span class="fd-text-xs fd-text-red-500">{{ $errors->{$bag}->first($dottedName) }}</span>
@endif