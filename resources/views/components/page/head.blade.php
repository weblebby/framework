@props(['back'])

<div {{ $attributes->class('flex items-start flex-col gap-5') }}>
    @if ((isset($actions) && filled($actions->toHtml())) || isset($back))
        <div class="flex gap-2">
            @if ($back ?? null)
                <x-feadmin::button
                    as="a"
                    :href="$back"
                    variant="outline-light"
                    icon="chevron-left"
                    size="sm"
                >@t('Geri dön', 'admin')</x-feadmin::button>
            @endif
            {{ $actions ?? null }}
        </div>
    @endif
    <div>
        {{ $slot }}
    </div>
</div>