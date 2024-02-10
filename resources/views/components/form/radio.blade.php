@props(['variant' => 'default'])
@aware(['name', 'bind', 'label', 'bag' => 'default'])
@aware(['default' => $bind->$name ?? null])

@php($id = \Weblebby\Framework\Support\FormComponent::id($name, $bag))
@php($name = \Weblebby\Framework\Support\FormComponent::dottedToName($name))
@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($name))

<label @class([
  'fd-inline-flex fd-items-center fd-gap-2' => $variant === 'default',
  'fd-cursor-pointer fd-flex fd-items-center fd-gap-3 fd-border-2 fd-rounded-lg fd-p-3 has-[:checked]:fd-bg-sky-100 has-[:checked]:fd-border-sky-600' => $variant === 'card',
])>
    <input
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $attributes
                ->merge([
                    'type' => 'radio',
                    'checked' => \Weblebby\Framework\Support\FormComponent::selected($dottedName, $default, $attributes),
                ])
                ->class('fd-peer fd-w-6 fd-h-6 fd-rounded-full fd-border-gray-300 fd-text-sky-600 fd-shadow-sm focus:fd-border-sky-300 focus:fd-ring focus:fd-ring-offset-0 focus:fd-ring-sky-200 focus:fd-ring-opacity-50') }}
    >
    @if ($label ?? false)
        <span @class(['peer-checked:fd-text-sky-600' => $variant === 'card'])>{{ $label }}</span>
    @endif
</label>