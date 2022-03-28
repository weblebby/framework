@props(['action', 'title', 'subtitle'])

<x-feadmin::modal {{ $attributes }}>
    <x-feadmin::modal.header
        :title="$title ?? t('Emin misiniz?', 'panel')"
        :subtitle="$subtitle ?? t('Bu işlem geri alınamaz, devam etmek istediğinize emin misiniz?', 'panel')"
    />
    <x-feadmin::modal.body class="fd-flex fd-items-center fd-gap-2">
        <x-feadmin::form :action="$action ?? false" method="DELETE">
            <x-feadmin::button type="submit" variant="red">@t('Sil', 'panel')</x-feadmin::button>
        </x-feadmin::form>
        <x-feadmin::button variant="light" data-modal-close>@t('Vazgeç', 'panel')</x-feadmin::button>
    </x-feadmin::modal.body>
</x-feadmin::modal>