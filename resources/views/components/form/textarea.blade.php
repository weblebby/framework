@props(['translatable' => false])
@aware(['name', 'bind', 'bag' => 'default', 'prefix' => null, 'suffix' => null])
@props(['default' => $bind->$name ?? null])

@php($id = \Weblebby\Framework\Support\FormComponent::id($name, $bag))
@php($name = \Weblebby\Framework\Support\FormComponent::dottedToName($name))
@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($name))
@php($value = old($dottedName, $default))

@if ($attributes->has('data-ckeditor'))
    @php($value = new \Illuminate\Support\HtmlString($value))
@endif

<div class="fd-flex fd-items-start fd-relative">
    @if ($prefix || $translatable)
        <x-weblebby::form.prefix class="fd-mt-2 -fd-mr-[1px] fd-rounded-l">
            @if ($translatable)
                <x-weblebby::form.prefix-translatable />
            @else
                {{ $prefix }}
            @endif
        </x-weblebby::form.prefix>
    @endif
    <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $attributes->class('fd-block fd-w-full fd-rounded-md fd-border-gray-300 fd-shadow-sm focus:fd-border-sky-300 focus:fd-ring focus:fd-ring-sky-200 focus:fd-ring-opacity-50') }}
    >{{ $value }}</textarea>
    @if ($suffix)
        <x-weblebby::form.prefix
                class="fd-mt-2 -fd-ml-[1px] fd-rounded-r"
                :suffix="true"
        >{{ $suffix }}</x-weblebby::form.prefix>
    @endif
</div>