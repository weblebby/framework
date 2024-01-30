@aware(['name', 'bind', 'label', 'bag' => 'default', 'useHiddenInput' => false])
@aware(['default' => $bind->$name ?? null])

@php($id = \Weblebby\Framework\Support\FormComponent::id($name, $bag))
@php($name = \Weblebby\Framework\Support\FormComponent::dottedToName($name))
@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($name))

@php($attributes = $attributes->merge(['value' => $default]))
@php($default = filled(old()) ? old($dottedName, $default) : $default)
@php($checked = \Weblebby\Framework\Support\FormComponent::selected($dottedName, $default, $attributes))

@if ($useHiddenInput)
    <input type="hidden" id="{{ $id }}" name="{{ $name }}" value="{{ $checked ? '1' : '0' }}">
@endif
<label class="fd-inline-flex fd-items-center">
    <input
            {{ $attributes
                ->merge([
                    'id' => $id,
                    'name' => $name,
                    'type' => 'checkbox',
                    'checked' => $checked,
                ])
                ->class('fd-w-6
                        fd-h-6
                        fd-rounded
                        fd-border-gray-300
                        fd-text-sky-600
                        fd-shadow-sm
                        focus:fd-border-sky-300
                        focus:fd-ring
                        focus:fd-ring-offset-0
                        focus:fd-ring-sky-200
                        focus:fd-ring-opacity-50') }}
    >
    @if ($label ?? false)
        <span class="fd-ml-2">{{ $label }}</span>
    @endif
</label>