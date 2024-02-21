<x-weblebby::layouts.panel>
    <x-weblebby::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-weblebby::page.head :back="panel_route('roles.index')">
            <x-weblebby::page.title>@lang('Create role')</x-weblebby::page.title>
            <x-weblebby::page.subtitle>@lang('Create a new user role')</x-weblebby::page.subtitle>
        </x-weblebby::page.head>
        <x-weblebby::form class="fd-space-y-3" :action="panel_route('roles.store')">
            <x-weblebby::card class="fd-space-y-3" padding>
                <x-weblebby::form.group name="name">
                    <x-weblebby::form.label>@lang('Role name')</x-weblebby::form.label>
                    <x-weblebby::form.input :placeholder="__('ex. Editor')" autofocus />
                </x-weblebby::form.group>
            </x-weblebby::card>
            <x-weblebby::card class="fd-space-y-5" padding>
                @foreach (panel()->permission()->get() as $key => $group)
                    <div>
                        <h3 class="fd-text-lg fd-font-medium fd-leading-none">{{ $group['title'] }}</h3>
                        @if ($group['description'] ?? null)
                            <span class="fd-text-zinc-600 fd-leading-none">{{ $group['description'] }}</span>
                        @endif
                        <div class="fd-flex fd-flex-col fd-gap-2 fd-mt-2">
                            @foreach ($group['permissions'] as $perm => $label)
                                <x-weblebby::form.group name="permissions[]">
                                    <x-weblebby::form.checkbox value="{{ $key }}:{{ $perm }}" :label="$label" />
                                </x-weblebby::form.group>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </x-weblebby::card>
            <x-weblebby::button type="submit">@lang('Create')</x-weblebby::button>
            <x-weblebby::form.sticky-submit />
        </x-weblebby::form>
    </x-weblebby::page>
</x-weblebby::layouts.panel>