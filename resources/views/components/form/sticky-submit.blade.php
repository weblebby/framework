@props(['fixed' => true, 'margin' => true, 'label' => __('Değişiklikleri kaydet')])

<div>
    @if ($fixed && $margin)
        <div class="fd-mt-20 md:fd-mt-14"></div>
    @endif
    <div {{ $attributes->class([
        'fd-px-10 fd-py-3 fd-border-t fd-border-zinc-200 fd-bg-zinc-100/50 fd-backdrop-blur fd-z-30',
        'fd-fixed fd-bottom-0 fd-left-0 md:fd-left-60 fd-right-0' => $fixed,
    ]) }}>
        <div class="fd-flex fd-justify-center md:fd-justify-end">
            <x-feadmin::button type="submit">{{ $label }}</x-feadmin::button>
        </div>
    </div>
</div>