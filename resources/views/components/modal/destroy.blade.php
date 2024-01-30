@props(['action', 'title', 'subtitle'])

<x-weblebby::modal {{ $attributes }}>
    <x-weblebby::modal.header
            :title="$title ?? __('Emin misiniz?')"
            :subtitle="$subtitle ?? __('Bu işlem geri alınamaz, devam etmek istediğinize emin misiniz?')"
    />
    <x-weblebby::modal.body class="fd-flex fd-items-center fd-gap-2">
        <x-weblebby::form :action="$action ?? false" method="DELETE">
            <x-weblebby::button type="submit" variant="red">@lang('Sil')</x-weblebby::button>
        </x-weblebby::form>
        <x-weblebby::button variant="light" data-modal-close>@lang('Vazgeç')</x-weblebby::button>
    </x-weblebby::modal.body>
</x-weblebby::modal>