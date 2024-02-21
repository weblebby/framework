<x-weblebby::layouts.panel>
    <x-weblebby::page class="lg:fd-w-2/3 fd-mx-auto">
        <x-weblebby::page.head :back="panel_route('roles.index')">
            <x-weblebby::page.title>{{ $role->name }}</x-weblebby::page.title>
        </x-weblebby::page.head>
        @if ($role->is_default)
            <x-weblebby::alert>
                <div>@lang('This role cannot be edited.')</div>
                @if ($role->name === 'Super Admin')
                    <div class="fd-font-normal">@lang('Users with the Super Admin role have all authorisations.')</div>
                @endif
            </x-weblebby::alert>
        @else
            <x-weblebby::form
                    class="fd-space-y-3"
                    :bind="$role"
                    :action="panel_route('roles.update', $role)"
                    method="PUT"
            >
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
                                        <x-weblebby::form.checkbox
                                                :label="$label"
                                                value="{{ $key }}:{{ $perm }}"
                                                :default="$role->permissions->pluck('name')->contains($key . ':' . $perm)"
                                        />
                                    </x-weblebby::form.group>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </x-weblebby::card>
                <x-weblebby::button type="submit">@lang('Save')</x-weblebby::button>
                <x-weblebby::form.sticky-submit />
            </x-weblebby::form>
        @endif
    </x-weblebby::page>
</x-weblebby::layouts.panel>