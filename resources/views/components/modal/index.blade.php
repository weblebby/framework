<div {{ $attributes
    ->class('fixed inset-0 bg-black/40 flex items-center justify-center z-50')
    ->merge(['style' => 'display:none', 'data-modal' => true]) }}>
    <div class="w-full max-w-xl bg-white rounded shadow-xl">
        {{ $slot }}
    </div>
</div>