@aware(['name', 'bind', 'bag' => 'default'])
@props(['type' => 'text', 'default' => $bind->$name ?? null])

@php($id = FormComponent::id($name))
@php($dottedName = FormComponent::dottedName($name))

<textarea
    id="{{ $id }}"
    name="{{ $name }}"
    {{ $attributes
        ->class('block
            w-full
            rounded-md
            border-gray-300
            shadow-sm
            focus:border-sky-300
            focus:ring
            focus:ring-sky-200
            focus:ring-opacity-50')
        ->class($errors->{$bag}->has($dottedName) ? 'border-red-500' : '') }}
>{{ old($dottedName, $default) }}</textarea>
