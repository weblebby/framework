@aware(['name', 'default'])

@php($dottedName = FormComponent::dottedName($name))
@php($oldValue = e(old($dottedName, $default ?? null)))

@if ((string) $oldValue === (string) $attributes->get('value') ? 'selected' : '')
    @php($attributes = $attributes->merge(['selected' => 'selected']))
@endif

<option {{ $attributes }}>{{ $slot }}</option>