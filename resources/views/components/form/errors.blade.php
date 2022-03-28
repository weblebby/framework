@aware(['name', 'bag'])

@error(FormComponent::dottedName($name), $bag)
    <span class="fd-text-xs fd-text-red-500">{{ $message }}</span>
@enderror