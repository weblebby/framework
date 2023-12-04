@aware(['name', 'bind', 'bag' => 'default'])
@props(['type' => 'text', 'default' => $bind->$name ?? null])

@php($id = \Feadmin\Support\FormComponent::id($name, $bag))
@php($name = \Feadmin\Support\FormComponent::dottedToName($name))
@php($dottedName = \Feadmin\Support\FormComponent::nameToDotted($name))

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
                fd-transition') }}
>