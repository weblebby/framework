@props(['options' => null])

@aware([
    'name' => null,
    'prefix' => null,
    'suffix' => null,
    'default' => null,
    'bind',
])

@aware(['default' => $bind->$name ?? null])

@unless(isset($options['name']))
    @php($options['name'] = $name)
@endunless

@if (isset($options['name']))
    @php($visualizedName = 'visualized_' . $options['name'] ?? null)
    @php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($visualizedName))
    @php($value = \Weblebby\Framework\Support\FormComponent::value(old($dottedName, $default)))
@endif

<div class="fd-flex fd-items-center fd-relative" data-tagify-container>
    @if ($prefix)
        <x-weblebby::form.prefix class="-fd-mr-[1px] fd-rounded-l">{{ $prefix }}</x-weblebby::form.prefix>
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
            'fd-bg-white',
            'fd-w-full',
            'fd-rounded-md',
            'fd-border-gray-300',
            'fd-shadow-sm',
            'fd-transition'
        ]) }}>
    @if ($suffix)
        <x-weblebby::form.prefix
                class="-fd-ml-[1px] fd-rounded-r"
                :suffix="true"
        >{{ $suffix }}</x-weblebby::form.prefix>
    @endif
</div>