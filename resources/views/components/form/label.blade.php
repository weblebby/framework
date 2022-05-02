@aware(['name', 'bag' => 'default'])

<label for="{{ FormComponent::id($name, $bag) }}" {{ $attributes }}>{{ $slot }}</label>