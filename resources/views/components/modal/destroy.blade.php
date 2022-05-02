@props(['action', 'title', 'subtitle'])

<x-feadmin::modal {{ $attributes }}>
    <x-feadmin::modal.header
        :title="$title ?? __('Emin misiniz?')"
        :subtitle="$subtitle ?? __('Bu işlem geri alınamaz, devam etmek istediğinize emin misiniz?')"
    />
    <x-feadmin::modal.body class="fd-flex fd-items-center fd-gap-2">
        <x-feadmin::form :action="$action ?? false" method="DELETE">
            <x-feadmin::button type="submit" variant="red">@lang('Sil')</x-feadmin::button>
        </x-feadmin::form>
        <x-feadmin::button variant="light" data-modal-close>@lang('Vazgeç')</x-feadmin::button>
    </x-feadmin::modal.body>
</x-feadmin::modal>