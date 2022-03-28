@aware(['name', 'bind', 'bag' => 'default'])
@props(['label', 'default' => $bind->$name ?? null])

@php($id = FormComponent::id($name, $bag))
@php($dottedName = FormComponent::dottedName($name))

<label class="fd-inline-flex fd-items-center">
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $attributes
            ->merge([
                'type' => 'radio',
                'checked' => FormComponent::checked($dottedName, $default, $attributes),
            ])
            ->class('fd-w-6
                    fd-h-6
                    fd-rounded-full
                    fd-border-gray-300
                    fd-text-sky-600
                    fd-shadow-sm
                    focus:fd-border-sky-300
                    focus:fd-ring
                    focus:fd-ring-offset-0
                    focus:fd-ring-sky-200
                    focus:fd-ring-opacity-50')
            ->class($errors->{$bag}->has($dottedName) ? 'fd-border-red-500' : '') }}
    >
    @if ($label ?? false)
        <span class="fd-ml-2">{{ $label }}</span>
    @endif
</label>