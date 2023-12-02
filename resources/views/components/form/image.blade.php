@aware(['name'])
@props(['name', 'image'])

<label class="fd-rounded-lg fd-overflow-hidden fd-block fd-cursor-pointer fd-relative" data-form-image>
    <input type="file" name="{{ $name }}" class="fd-hidden" accept="image/*">
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