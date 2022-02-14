@aware(['name', 'bind', 'bag' => 'default'])
@props(['label', 'default' => $bind->$name ?? null])

@php($id = FormComponent::id($name))
@php($dottedName = FormComponent::dottedName($name))

<label class="inline-flex items-center">
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $attributes
            ->merge([
                'type' => 'checkbox',
                'value' => '1',
                'checked' => FormComponent::checked($dottedName, $default, $attributes),
            ])
            ->class('w-6 h-6
                    rounded
                    border-gray-300
                    text-sky-600
                    shadow-sm
                    focus:border-sky-300
                    focus:ring
                    focus:ring-offset-0
                    focus:ring-sky-200
                    focus:ring-opacity-50')
            ->class($errors->{$bag}->has($dottedName) ? 'border-red-500' : '') }}
    >
    @if ($label ?? false)
        <span class="ml-2">{{ $label }}</span>
    @endif
</label>