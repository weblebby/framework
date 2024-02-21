@props(['action', 'title', 'subtitle'])

<x-weblebby::modal {{ $attributes }}>
    <x-weblebby::modal.header
            :title="$title ?? __('Are you sure?')"
            :subtitle="$subtitle ?? __('Are you sure you want to continue? This action cannot be undone.')"
    />
    <x-weblebby::modal.body class="fd-flex fd-items-center fd-gap-2">
        <x-weblebby::form :action="$action ?? false" method="DELETE">
            <x-weblebby::button type="submit" variant="red">@lang('Delete')</x-weblebby::button>
        </x-weblebby::form>
        <x-weblebby::button variant="light" data-modal-close>@lang('Cancel')</x-weblebby::button>
    </x-weblebby::modal.body>
</x-weblebby::modal>