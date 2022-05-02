@aware(['name', 'default' => null])

<option {{ $attributes
    ->merge(['selected' => FormComponent::selected(
        FormComponent::dottedName($name),
        $default,
        $attributes
    )]) }}>{{ $slot }}</option>