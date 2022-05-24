<div {{ $attributes
    ->class('fd-fixed fd-inset-0 fd-bg-black/40 fd-flex fd-items-center fd-justify-center fd-z-50')
    ->merge(['style' => 'display:none', 'data-modal' => true]) }}>
    <div class="fd-w-full fd-max-w-xl fd-bg-white sm:fd-rounded fd-shadow-xl">
        {{ $slot }}
    </div>
</div>