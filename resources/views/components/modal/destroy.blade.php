@props(['action', 'title', 'subtitle'])

<x-feadmin::modal {{ $attributes }}>
    <x-feadmin::modal.header
        :title="$title ?? t('Emin misiniz?', 'admin')"
        :subtitle="$subtitle ?? t('Bu işlem geri alınamaz, devam etmek istediğinize emin misiniz?', 'admin')"
    />
    <x-feadmin::modal.body class="flex items-center gap-2">
        <x-feadmin::form :action="$action ?? false" method="DELETE">
            <x-feadmin::button type="submit" variant="red">@t('Sil', 'admin')</x-feadmin::button>
        </x-feadmin::form>
        <x-feadmin::button variant="light" data-modal-close>@t('Vazgeç', 'admin')</x-feadmin::button>
    </x-feadmin::modal.body>
</x-feadmin::modal>