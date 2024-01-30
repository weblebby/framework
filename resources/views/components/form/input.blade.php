@props(['translatable' => false])

@aware([
    'name' => null,
    'bag' => 'default',
    'type' => 'text',
    'prefix' => null,
    'suffix' => null,
    'bind',
])

@aware(['default' => $bind->$name ?? request($name)])

@php($id = \Weblebby\Framework\Support\FormComponent::id($name, $bag))
@php($name = \Weblebby\Framework\Support\FormComponent::dottedToName($name))
@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($name))

<div class="fd-flex fd-items-center fd-relative">
    @if ($prefix || $translatable)
        <x-weblebby::form.prefix class="-fd-mr-[1px] fd-rounded-l">
            @if ($translatable)
                <x-weblebby::form.prefix-translatable />
            @else
                {{ $prefix }}
            @endif
        </x-weblebby::form.prefix>
    @endif
    <input {{ $attributes
        ->merge([
            'type' => $type,
            'value' => FormComponent::value(isset($name) ? old($dottedName, $default) : $default),
            'id' => $id ?? false,
            'name' => $name ?? false,
        ])
        ->class('fd-block
            fd-w-full
            fd-rounded-md
            fd-border-gray-300
            fd-shadow-sm
            focus:fd-border-sky-300
            focus:fd-ring
            focus:fd-ring-sky-200
            focus:fd-ring-opacity-50
            read-only:fd-opacity-70
            read-only:fd-bg-zinc-200
            fd-transition') }}>
    @if ($suffix)
        <x-weblebby::form.prefix class="-fd-ml-[1px] fd-rounded-r"
                                 :suffix="true">{{ $suffix }}</x-weblebby::form.prefix>
    @endif
</div>