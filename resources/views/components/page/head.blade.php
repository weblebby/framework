@props(['back'])

<div {{ $attributes->class('fd-flex fd-items-start fd-flex-col fd-gap-5') }}>
    <div class="fd-w-full fd-flex fd-items-center fd-justify-between fd-gap-2">
        <div class="fd-flex fd-items-center fd-gap-3">
            @if (isset($back))
                <x-feadmin::button
                        as="a"
                        :href="$back"
                        variant="outline-light"
                        icon="chevron-left"
                />
            @endif
            <div>
                {{ $slot }}
            </div>
        </div>
        <div class="fd-flex fd-items-center fd-gap-2">
            {{ $actions ?? null }}
        </div>
    </div>
    @hook(panel()->nameWith('after_page_actions'))
</div>