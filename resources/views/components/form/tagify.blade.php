@aware([
    'name' => null,
    'bag' => 'default',
    'type' => 'text',
    'prefix' => null,
    'suffix' => null,
    'bind',
])

@aware(['default' => $bind->$name ?? null])

@props(['options' => null])

@php($id = \Feadmin\Support\FormComponent::id($name, $bag))
@php($name = \Feadmin\Support\FormComponent::dottedToName($name))
@php($dottedName = \Feadmin\Support\FormComponent::nameToDotted($name))

<div class="fd-flex fd-items-center" data-tagify-container>
    @if ($prefix)
        <x-feadmin::form.prefix class="-fd-mr-[1px] fd-rounded-l">{{ $prefix }}</x-feadmin::form.prefix>
    @endif
    <input {{ $attributes
        ->merge([
            'type' => $type,
            'data-tagify' => is_array($options) ? json_encode($options) : true,
            'value' => \Feadmin\Support\FormComponent::value(isset($name) ? old($dottedName, $default) : $default),
            'id' => $id ?? false,
            'name' => $name ?? false,
        ])
        ->class([
            'fd-block',
            'fd-w-full',
            'fd-rounded-md',
            'fd-border-gray-300',
            'fd-shadow-sm',
            'fd-transition'
        ]) }}>
    @if ($suffix)
        <x-feadmin::form.prefix class="-fd-ml-[1px] fd-rounded-r">{{ $suffix }}</x-feadmin::form.prefix>
    @endif
</div>