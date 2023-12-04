@aware(['name', 'bind', 'default' => null])
@aware(['default' => $bind->$name ?? request($name)])

<option {{ $attributes
    ->merge(['selected' => \Feadmin\Support\FormComponent::selected(
        \Feadmin\Support\FormComponent::nameToDotted($name),
        $default,
        $attributes
    )]) }}>{{ $slot }}</option>