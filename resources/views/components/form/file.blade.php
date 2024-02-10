@aware(['name', 'bind', 'bag' => 'default'])
@props(['type' => 'text', 'default' => $bind->$name ?? null])

@php($id = \Weblebby\Framework\Support\FormComponent::id($name, $bag))
@php($name = \Weblebby\Framework\Support\FormComponent::dottedToName($name))
@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($name))

<div class="fd-p-3 fd-bg-zinc-100 fd-rounded fd-space-y-2">
    <input
            {{ $attributes
                ->merge([
                    'type' => 'file',
                    'id' => $id ?? false,
                    'name' => $name ?? false,
                    'data-file-input' => true,
                ])
                ->class('fd-block
                    fd-w-full
                    focus:fd-border-sky-300
                    focus:fd-ring
                    focus:fd-ring-sky-200
                    focus:fd-ring-opacity-50
                    fd-transition') }}
    >
    @if ($default)
        <x-weblebby::form.file-default :default="$default" />
    @endif
</div>