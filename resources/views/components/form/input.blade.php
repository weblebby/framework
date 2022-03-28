@aware(['name' => null, 'bind', 'bag' => 'default'])
@props(['type' => 'text', 'default' => $bind->$name ?? null])

@php($id = FormComponent::id($name, $bag))
@php($dottedName = FormComponent::dottedName($name))

<input
    {{ $attributes
        ->merge([
            'type' => $type,
            'value' => isset($name) ? old($dottedName, $default) : $default,
            'id' => $id ?? false,
            'name' => $name ?? false,
        ])
        ->class('block
            w-full
            rounded-md
            border-gray-300
            shadow-sm
            focus:border-sky-300
            focus:ring
            focus:ring-sky-200
            focus:ring-opacity-50
            read-only:opacity-70
            read-only:bg-zinc-200
            transition')
        ->class($errors->{$bag}->has($dottedName) ? 'border-red-500' : '') }}
>