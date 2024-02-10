<x-weblebby::layouts.master>
    <div class="fd-h-full fd-flex fd-flex-col fd-items-center fd-justify-center">
        <div class="fd-w-full md:fd-w-[40rem] fd-px-6">
            <div class="fd-text-center fd-mb-12">
                <x-weblebby::page.title>@lang('Build your site quickly')</x-weblebby::page.title>
                <x-weblebby::page.subtitle>@lang('This is the starting point where all difficulties are left behind. ')</x-weblebby::page.subtitle>
            </div>
            <x-weblebby::form :action="panel()->route('setup.update')" method="PUT">
                <input type="hidden" name="step" value="{{ $currentStep }}">
                <div class="fd-space-y-6">
                    <x-weblebby::progress :currentStep="$currentStep">
                        @foreach ($steps as $key => $value)
                            <x-weblebby::progress.item
                                    :iteration="$loop->iteration"
                                    :step="$key"
                                    :url="panel()->route('setup.index', ['step' => $key])"
                            >{{ $value }}</x-weblebby::progress.item>
                        @endforeach
                    </x-weblebby::progress>
                    <x-weblebby::card class="fd-space-y-3" padding>
                        @if ($currentStep === 'site')
                            <p class="fd-text-lg">@lang('Configure your site.')</p>
                            @foreach ($fields as $field)
                                <x-weblebby::form.field :field="$field" />
                            @endforeach
                        @elseif($currentStep === 'theme')
                            <p class="fd-text-lg">@lang('Make your theme preferences.')</p>
                            @foreach ($fields as $field)
                                <x-weblebby::form.field :field="$field" />
                            @endforeach
                        @elseif($currentStep === 'variant')
                            <p class="fd-text-lg">@lang('Choose a variant for the :theme theme.', ['theme' => theme()->title()])</p>
                            <div class="fd-space-y-3">
                                <x-weblebby::form.group name="variant">
                                    @foreach (theme()->getVariants() as $variant)
                                        <x-weblebby::form.radio
                                                variant="card"
                                                :label="$variant->title()"
                                                :value="$variant->name()"
                                        />
                                    @endforeach
                                </x-weblebby::form.group>
                            </div>
                        @endif
                    </x-weblebby::card>
                    <div class="fd-flex fd-items-center fd-justify-between fd-gap-2">
                        @if ($prevStep)
                            <x-weblebby::button
                                    as="a"
                                    :href="panel()->route('setup.index', ['step' => $prevStep])"
                                    variant="light"
                                    icon="chevron-left"
                            >@lang('Go back')</x-weblebby::button>
                        @else
                            <div></div>
                        @endif
                        <x-weblebby::button type="submit">@lang('Continue')</x-weblebby::button>
                    </div>
                </div>
            </x-weblebby::form>
        </div>
    </div>
</x-weblebby::layouts.master>