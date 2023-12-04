@aware([
    'name' => null,
    'bag' => 'default',
    'type' => 'text',
    'prefix' => null,
    'suffix' => null,
    'bind',
])

@aware(['default' => $bind->$name ?? request($name)])

@php($id = \Feadmin\Support\FormComponent::id($name, $bag))
@php($name = \Feadmin\Support\FormComponent::dottedToName($name))
@php($dottedName = \Feadmin\Support\FormComponent::nameToDotted($name))

<div class="fd-flex fd-items-center">
    @if ($prefix)
        <x-feadmin::form.prefix class="-fd-mr-[1px] fd-rounded-l">{{ $prefix }}</x-feadmin::form.prefix>
    @endif
    <input {{ $attributes
        ->merge([
            'type' => $type,
            'value' => FormComponent::value(isset($name) ? old($dottedName, $default) : $default),
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
            fd-transition') }}>
    @if ($suffix)
        <x-feadmin::form.prefix class="-fd-ml-[1px] fd-rounded-r">{{ $suffix }}</x-feadmin::form.prefix>
    @endif
</div>