<x-weblebby::layouts.panel>
    <x-weblebby::page id="post" class="fd-mx-auto">
        <x-weblebby::page.head :back="panel_route('posts.index', ['type' => $postable::getModelName()])">
            <x-weblebby::page.title>@lang(':name oluşturun', ['name' => $postable::getSingularName()])</x-weblebby::page.title>
        </x-weblebby::page.head>
        <x-weblebby::form :action="panel_route('posts.store')" enctype="multipart/form-data">
            @hook(panel()->nameWith('post:create:before_form_fields'))
            @hook(panel()->nameWith('post:before_form_fields'))
            <input type="hidden" name="postable" value="{{ $postable::getModelName() }}">
            <div class="fd-flex fd-gap-3">
                <div class="fd-w-2/3 fd-space-y-2">
                    <x-weblebby::form.group name="title">
                        <x-weblebby::form.input
                                :placeholder="__('Başlık')"
                                :translatable="$isTranslatable"
                                autofocus
                        />
                    </x-weblebby::form.group>
                    <x-weblebby::form.group class="ck-editor--xl" name="content">
                        <x-weblebby::form.textarea
                                :placeholder="__('İçerik')"
                                :translatable="$isTranslatable"
                                data-ckeditor
                        />
                    </x-weblebby::form.group>
                    <x-weblebby::tabs container="post" :default="array_keys($sections)[0] ?? null">
                        <x-weblebby::tabs.header>
                            @foreach ($sections as $id => $section)
                                <x-weblebby::tabs.button :id="$id">{{ $section['title'] }}</x-weblebby::tabs.button>
                            @endforeach
                        </x-weblebby::tabs.header>
                        @foreach($sections as $id => $section)
                            <x-weblebby::tabs.content :for="$id">
                                <div class="fd-space-y-3">
                                    @foreach ($section['fields'] as $field)
                                        <x-weblebby::form.field :field="$field" />
                                    @endforeach
                                </div>
                            </x-weblebby::tabs.content>
                        @endforeach
                    </x-weblebby::tabs>
                </div>
                <div class="fd-flex-1 fd-space-y-3">
                    <x-weblebby::card padding>
                        <x-weblebby::card.title>@lang('Durum')</x-weblebby::card.title>
                        <x-weblebby::form.group name="status">
                            <x-weblebby::form.select>
                                @foreach (\Weblebby\Framework\Enums\PostStatusEnum::cases() as $status)
                                    <x-weblebby::form.option
                                            value="{{ $status->value }}"
                                            :data-hint="$status->hint()">{{ $status->label() }}</x-weblebby::form.option>
                                @endforeach
                            </x-weblebby::form.select>
                            <x-weblebby::form.hint data-status-hint></x-weblebby::form.hint>
                        </x-weblebby::form.group>
                    </x-weblebby::card>
                    @if ($categoryTax = $postable::getTaxonomyFor('category'))
                        <x-weblebby::card padding>
                            <x-weblebby::card.title>@lang('Kategori')</x-weblebby::card.title>
                            <x-weblebby::taxonomies
                                    for="category"
                                    :taxonomies="$categories"
                                    :taxonomyItem="$categoryTax"
                            />
                        </x-weblebby::card>
                    @endif
                    @if ($tagTax = $postable::getTaxonomyFor('tag'))
                        <x-weblebby::card padding>
                            <x-weblebby::card.title>@lang('Etiketler')</x-weblebby::card.title>
                            <x-weblebby::form.tagify :options="[
                                'source' => panel_api_route('taxonomies.index', $tagTax->name()),
                                'map' => ['value' => 'taxonomy_id', 'label' => 'title'],
                                'name' => sprintf('taxonomies[%s][]', $tagTax->name()),
                            ]" />
                        </x-weblebby::card>
                    @endif
                    @if ($postable::doesSupportTemplates() && panel()->supports(\Weblebby\Framework\Support\Features::themes()))
                        <x-weblebby::card padding>
                            <x-weblebby::card.title>@lang('Şablon')</x-weblebby::card.title>
                            <x-weblebby::form.group name="template">
                                <x-weblebby::form.select data-post-type="{{ $postable::class }}">
                                    <x-weblebby::form.option value="">@lang('Varsayılan')</x-weblebby::form.option>
                                    @foreach ($templates as $template)
                                            <?php /** @var \Weblebby\Framework\Abstracts\Theme\Template $template */ ?>
                                        <x-weblebby::form.option
                                                :value="$template->name()">{{ $template->title() }}</x-weblebby::form.option>
                                    @endforeach
                                </x-weblebby::form.select>
                                <x-weblebby::form.hint>@lang('Temanızın destekliği şablonları kullanarak sayfa düzenini değiştirebilirsiniz.')</x-weblebby::form.hint>
                            </x-weblebby::form.group>
                        </x-weblebby::card>
                    @endif
                    <x-weblebby::card padding>
                        <x-weblebby::card.title>@lang('Önerilen görsel')</x-weblebby::card.title>
                        <x-weblebby::form.image name="featured_image" />
                    </x-weblebby::card>
                </div>
            </div>
            <x-weblebby::form.sticky-submit />
        </x-weblebby::form>
    </x-weblebby::page>
    @push('after_scripts')
        <x-weblebby::tabs.template />
        @if (panel()->supports(\Weblebby\Framework\Support\Features::themes()))
            <script>
              window.Weblebby.Theme = {
                postFieldsUrl: @json(panel_route('themes.templates.post-fields', [':theme', ':template']))
              };
            </script>
        @endif
        @vite('resources/js/pages/post/post.js', 'weblebby/build')
        @if($isCodeEditorNeeded)
            @vite('resources/js/code-editor.js', 'weblebby/build')
        @endif
    @endpush
</x-weblebby::layouts.panel>
