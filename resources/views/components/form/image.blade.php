@aware(['name', 'bind' => null, 'bag' => 'default', 'prefix' => null, 'suffix' => null])
@props(['name', 'image'])

@php($name = \Feadmin\Support\FormComponent::dottedToName($name))
@php($dottedName = \Feadmin\Support\FormComponent::nameToDotted($name))
@php($id = \Feadmin\Support\FormComponent::id($name, $bag))

@if ($bind instanceof \Spatie\MediaLibrary\HasMedia && blank($image ?? null))
    @php($dottedNameWithoutLastWildcard = \Feadmin\Support\FormComponent::nameToDottedWithoutEmptyWildcard($dottedName))
    @php($dottedNameParts = array_reverse(explode('.', $dottedNameWithoutLastWildcard)))
    @php($image = $bind->getFirstMediaUrl(head($dottedNameParts)))
@endif

<div class="fd-flex fd-items-start">
    @if ($prefix)
        <x-feadmin::form.prefix class="fd-mt-2 -fd-mr-[1px] fd-rounded-l">{{ $prefix }}</x-feadmin::form.prefix>
    @endif
    <label class="fd-flex-1 fd-rounded-lg fd-overflow-hidden fd-block fd-cursor-pointer fd-relative" data-form-image>
        <input type="file" id="{{ $id }}" name="{{ $name }}" class="fd-hidden" accept="image/*">
        <div class="fd-h-60" data-image-wrapper>
            @if ($image ?? null)
                <img class="fd-w-full fd-h-full fd-object-cover" src="{{ $image }}" alt="Uploaded image">
            @endif
        </div>
        <div class="fd-absolute fd-inset-0 fd-text-white fd-bg-black/40 fd-flex fd-flex-col fd-gap-3 fd-items-center fd-justify-center">
            <x-feadmin::icons.upload class="fd-w-8 fd-h-8" />
            <span class="fd-font-medium">{{ isset($image) ? __('Görseli değiştir') : __('Görsel yükle') }}</span>
        </div>
    </label>
    @if ($suffix)
        <x-feadmin::form.prefix class="fd-mt-2 -fd-ml-[1px] fd-rounded-r">{{ $suffix }}</x-feadmin::form.prefix>
    @endif
</div>