@aware(['name', 'bind', 'bag' => 'default'])
@props(['type' => 'text', 'default' => $bind->$name ?? null])

@php($id = \Weblebby\Framework\Support\FormComponent::id($name, $bag))
@php($name = \Weblebby\Framework\Support\FormComponent::dottedToName($name))
@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($name))

<input
        type="hidden"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ old($dottedName, $default) }}"
        data-code-editor-value
        hidden
>
<div {{ $attributes->merge(['id' => "code-editor--{$id}"]) }}></div>