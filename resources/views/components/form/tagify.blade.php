@props(['options' => null])

@aware([
    'name' => null,
    'prefix' => null,
    'suffix' => null,
    'default' => null,
])

@if (isset($options['name']))
    @php($visualizedName = 'visualized_' . $options['name'] ?? null)
    @php($dottedName = \Feadmin\Support\FormComponent::nameToDotted($visualizedName))
    @php($value = \Feadmin\Support\FormComponent::value(old($dottedName, $default)))
@endif

<div class="fd-flex fd-items-center" data-tagify-container>
    @if ($prefix)
        <x-feadmin::form.prefix class="-fd-mr-[1px] fd-rounded-l">{{ $prefix }}</x-feadmin::form.prefix>
    @endif
    <input {{ $attributes
        ->merge([
            'type' => 'text',
            'data-tagify' => is_array($options) ? json_encode($options) : true,
            'value' => $value ?? false,
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