@aware(['name', 'bind', 'bag' => 'default'])
@props(['type' => 'text', 'default' => $bind->$name ?? null])

@php($id = FormComponent::id($name, $bag))
@php($dottedName = FormComponent::dottedName($name))

<input
    {{ $attributes
        ->merge([
            'type' => 'file',
            'id' => $id ?? false,
            'name' => $name ?? false,
        ])
        ->class('block
            w-full
            focus:border-sky-300
            focus:ring
            focus:ring-sky-200
            focus:ring-opacity-50
            transition')
        ->class($errors->{$bag}->has($dottedName) ? 'border-red-500' : '') }}
>