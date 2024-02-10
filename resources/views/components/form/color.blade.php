@aware([
    'name' => null,
    'bag' => 'default',
    'bind',
])

@aware(['default' => $bind->$name ?? request($name)])

@php($id = \Weblebby\Framework\Support\FormComponent::id($name, $bag))
@php($name = \Weblebby\Framework\Support\FormComponent::dottedToName($name))
@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($name))

<div class="fd-flex fd-items-center fd-relative">
    <input {{ $attributes
        ->merge([
            'type' => 'color',
            'value' => FormComponent::value(isset($name) ? old($dottedName, $default) : $default),
            'id' => $id ?? false,
            'name' => $name ?? false,
        ])
        ->class('fd-p-1 fd-h-10 fd-w-14 fd-block fd-bg-white fd-border fd-border-gray-200 fd-cursor-pointer fd-rounded-lg fd-disabled:opacity-50 fd-disabled:pointer-events-none') }}>
</div>