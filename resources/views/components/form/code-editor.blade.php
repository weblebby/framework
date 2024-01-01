@aware(['name', 'bind', 'bag' => 'default'])
@props(['type' => 'text', 'default' => $bind->$name ?? null])

@php($id = \Feadmin\Support\FormComponent::id($name, $bag))
@php($name = \Feadmin\Support\FormComponent::dottedToName($name))
@php($dottedName = \Feadmin\Support\FormComponent::nameToDotted($name))

<input type="hidden" id="{{ $id }}" name="{{ $name }}" value="{{ old($dottedName, $default) }}" data-code-editor-value>
<div
        {{ $attributes
            ->merge(['id' => "code-editor--{$id}"])
            ->class('fd-h-60') }}
></div>