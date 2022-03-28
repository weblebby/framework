@aware(['name'])
@props(['name', 'image'])

<label class="rounded-lg overflow-hidden block cursor-pointer relative" data-form-image>
    <input type="file" name="{{ $name }}" class="hidden" />
    <div class="h-60" data-image-wrapper>
        @if ($image ?? null)
            <img class="w-full h-full object-cover" src="{{ $image }}">
        @endif
    </div>
    <div class="absolute inset-0 text-white bg-black/40 flex flex-col gap-3 items-center justify-center">
        <x-feadmin::icons.upload class="w-8 h-8" />
        <span class="font-medium">{{ isset($image) ? t('Görseli değiştir', 'panel') : t('Görsel yükle', 'panel') }}</span>
    </div>
</label>