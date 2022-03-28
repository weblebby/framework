@props(['back'])

<div {{ $attributes->class('fd-flex fd-items-start fd-flex-col fd-gap-5') }}>
    @if ((isset($actions) && filled($actions->toHtml())) || isset($back))
        <div class="fd-flex fd-gap-2">
            @if ($back ?? null)
                <x-feadmin::button
                    as="a"
                    :href="$back"
                    variant="outline-light"
                    icon="chevron-left"
                    size="sm"
                >@t('Geri dön', 'panel')</x-feadmin::button>
            @endif
            {{ $actions ?? null }}
        </div>
    @endif
    <div>
        {{ $slot }}
    </div>
</div>