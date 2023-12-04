@aware([
    'name' => null,
    'bag' => 'default',
    'bind',
])

@aware(['default' => $bind->$name ?? request($name)])

@php($id = \Feadmin\Support\FormComponent::id($name, $bag))
@php($name = \Feadmin\Support\FormComponent::dottedToName($name))
@php($dottedName = \Feadmin\Support\FormComponent::nameToDotted($name))

<select
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $attributes
            ->class('fd-block
                fd-w-full
                fd-rounded-md
                fd-border-gray-300
                fd-shadow-sm
                focus:fd-border-sky-300
                focus:fd-ring
                focus:fd-ring-sky-200
                focus:fd-ring-opacity-50') }}
>
    {{ $slot }}
</select>
