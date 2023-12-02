@aware(['name', 'bind', 'default' => null])
@aware(['default' => $bind->$name ?? request($name)])

<option {{ $attributes
    ->merge(['selected' => FormComponent::selected(
        FormComponent::dottedName($name),
        $default,
        $attributes
    )]) }}>{{ $slot }}</option>