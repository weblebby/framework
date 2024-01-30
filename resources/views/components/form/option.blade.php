@aware(['name', 'bind', 'default' => null])
@aware(['default' => $bind->$name ?? request($name)])

<option {{ $attributes
    ->merge(['selected' => \Weblebby\Framework\Support\FormComponent::selected(
        \Weblebby\Framework\Support\FormComponent::nameToDotted($name),
        $default,
        $attributes
    )]) }}>{{ $slot }}</option>