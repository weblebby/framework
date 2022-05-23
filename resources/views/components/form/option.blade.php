@aware(['name', 'bind', 'default' => null])
@aware(['default' => $bind->$name ?? null])

<option {{ $attributes
    ->merge(['selected' => FormComponent::selected(
        FormComponent::dottedName($name),
        $default,
        $attributes
    )]) }}>{{ $slot }}</option>