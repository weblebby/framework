@aware(['name', 'bag'])

@error(FormComponent::dottedName($name), $bag)
    <span class="text-xs text-red-500">{{ $message }}</span>
@enderror