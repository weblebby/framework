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

@php($id = FormComponent::id($name, $bag))
@php($dottedName = FormComponent::dottedName($name))

<div class="fd-flex fd-items-center" data-tagify-container>
    @if ($prefix)
        <x-feadmin::form.prefix class="-fd-mr-[1px] fd-rounded-l">{{ $prefix }}</x-feadmin::form.prefix>
    @endif
    <input {{ $attributes
        ->merge([
            'type' => $type,
            'data-tagify' => is_array($options) ? json_encode($options) : true,
            'value' => FormComponent::value(isset($name) ? old($dottedName, $default) : $default),
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
        ])
        ->class($dottedName && $errors->{$bag}->has($dottedName) ? 'fd-border-red-500' : '') }}>
    @if ($suffix)
        <x-feadmin::form.prefix class="-fd-ml-[1px] fd-rounded-r">{{ $suffix }}</x-feadmin::form.prefix>
    @endif
</div>