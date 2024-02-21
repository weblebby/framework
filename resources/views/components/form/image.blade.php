@props(['translatable' => false])
@aware(['name', 'bind' => null, 'bag' => 'default', 'prefix' => null, 'suffix' => null])
@props(['name', 'image'])

@php($name = \Weblebby\Framework\Support\FormComponent::dottedToName($name))
@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($name))
@php($id = \Weblebby\Framework\Support\FormComponent::id($name, $bag))

@if ($bind instanceof \Spatie\MediaLibrary\HasMedia && blank($image ?? null))
    @php($dottedNameWithoutLastWildcard = \Weblebby\Framework\Support\FormComponent::nameToDottedWithoutEmptyWildcard($dottedName))
    @php($dottedNameParts = array_reverse(explode('.', $dottedNameWithoutLastWildcard)))
    @php($image = $bind->getFirstMediaUrl(head($dottedNameParts)))
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
    <label class="fd-flex-1 fd-rounded-lg fd-overflow-hidden fd-block fd-cursor-pointer fd-relative" data-form-image>
        <input type="file" id="{{ $id }}" name="{{ $name }}" class="fd-hidden" accept="image/*">
        <div class="fd-h-60" data-image-wrapper>
            @if ($image ?? null)
                @php($image = $image instanceof \Spatie\MediaLibrary\MediaCollections\Models\Media ? $image->getUrl() : $image)
                <img class="fd-w-full fd-h-full fd-object-cover" src="{{ $image }}" alt="Uploaded image">
            @endif
        </div>
        <div class="fd-absolute fd-inset-0 fd-text-white fd-bg-black/40 fd-flex fd-flex-col fd-gap-3 fd-items-center fd-justify-center">
            <x-weblebby::icons.upload class="fd-w-8 fd-h-8" />
            <span class="fd-font-medium">{{ isset($image) ? __('Change image') : __('Upload image') }}</span>
        </div>
    </label>
    @if ($suffix)
        <x-weblebby::form.prefix
                class="fd-mt-2 -fd-ml-[1px] fd-rounded-r"
                :suffix="true"
        >{{ $suffix }}</x-weblebby::form.prefix>
    @endif
</div>