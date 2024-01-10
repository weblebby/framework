@props(['translatable' => false])
@aware(['name', 'bind', 'bag' => 'default', 'prefix' => null, 'suffix' => null])
@props(['default' => $bind->$name ?? null])

@php($id = \Feadmin\Support\FormComponent::id($name, $bag))
@php($name = \Feadmin\Support\FormComponent::dottedToName($name))
@php($dottedName = \Feadmin\Support\FormComponent::nameToDotted($name))

<div class="fd-flex fd-items-start">
    @if ($prefix || $translatable)
        <x-feadmin::form.prefix class="fd-mt-2 -fd-mr-[1px] fd-rounded-l">
            @if ($translatable)
                <x-feadmin::form.prefix-translatable />
            @else
                {{ $prefix }}
            @endif
        </x-feadmin::form.prefix>
    @endif
    <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $attributes->class('fd-block fd-w-full fd-rounded-md fd-border-gray-300 fd-shadow-sm focus:fd-border-sky-300 focus:fd-ring focus:fd-ring-sky-200 focus:fd-ring-opacity-50') }}
    >{{ old($dottedName, $default) }}</textarea>
    @if ($suffix)
        <x-feadmin::form.prefix class="fd-mt-2 -fd-ml-[1px] fd-rounded-r">{{ $suffix }}</x-feadmin::form.prefix>
    @endif
</div>