@aware([
    'name' => null,
    'bag' => 'default',
    'type' => 'text',
    'bind',
])

@aware(['default' => $bind->$name ?? null])

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
        ->class('fd-block
            fd-w-full
            fd-rounded-md
            fd-border-gray-300
            fd-shadow-sm
            focus:fd-border-sky-300
            focus:fd-ring
            focus:fd-ring-sky-200
            focus:fd-ring-opacity-50
            read-only:fd-opacity-70
            read-only:fd-bg-zinc-200
            fd-transition')
        ->class($errors->{$bag}->has($dottedName) ? 'fd-border-red-500' : '') }}
>