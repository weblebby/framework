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
        ->class('fd-block
            fd-w-full
            focus:fd-border-sky-300
            focus:fd-ring
            focus:fd-ring-sky-200
            focus:fd-ring-opacity-50
            fd-transition')
        ->class($dottedName && $errors->{$bag}->has($dottedName) ? 'fd-border-red-500' : '') }}
>