<x-weblebby::layouts.panel>
    <x-weblebby::page id="post" class="fd-mx-auto">
        <x-weblebby::page.head :back="panel_route('posts.index', ['type' => $post::getModelName()])">
            <x-weblebby::page.title>@lang('Düzenle: :name', ['name' => $post->title])</x-weblebby::page.title>
        </x-weblebby::page.head>
        <x-weblebby::form
                :bind="$post"
                :action="panel_route('posts.update', $post)"
                method="PUT"
                enctype="multipart/form-data"
        >
            @hook(panel()->nameWith('post:edit:before_form_fields'))
            @hook(panel()->nameWith('post:before_form_fields'))
            <div class="fd-flex fd-gap-3">
                <div class="fd-w-2/3 fd-space-y-2">
                    <x-weblebby::form.group name="title">
                        <x-weblebby::form.input
                                :default="$translatedPost->title"
                                :placeholder="__('Başlık')"
                                :translatable="$isTranslatable"
                                autofocus
                        />
                    </x-weblebby::form.group>
                    <x-weblebby::form.group class="ck-editor--xl" name="content">
                        <x-weblebby::form.textarea
                                :default="$translatedPost->content"
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
                                        <x-weblebby::form.field
                                                :field="$field"
                                                :default="$metafields[$field['key']] ?? null"
                                        />
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
                    @if ($categoryTax = $post::getTaxonomyFor('category'))
                        <x-weblebby::card padding>
                            <x-weblebby::card.title>@lang('Kategori')</x-weblebby::card.title>
                            <x-weblebby::taxonomies
                                    for="category"
                                    :primary="$post->primaryTaxonomy"
                                    :taxonomies="$categories"
                                    :taxonomyItem="$categoryTax"
                            />
                        </x-weblebby::card>
                    @endif
                    @if ($tagTax = $post::getTaxonomyFor('tag'))
                        <x-weblebby::card padding>
                            <x-weblebby::card.title>@lang('Etiketler')</x-weblebby::card.title>
                            <x-weblebby::form.tagify
                                    :value="$post->getTaxonomiesFor($tagTax->name(), $locale)->pluck('term.title')"
                                    :options="[
                                        'source' => panel_api_route('taxonomies.index', [$tagTax->name(), '_locale' => $locale]),
                                        'map' => ['value' => 'taxonomy_id', 'label' => 'title'],
                                        'name' => sprintf('taxonomies[%s][]', $tagTax->name()),
                                    ]"
                            />
                        </x-weblebby::card>
                    @endif
                    @if ($post::doesSupportTemplates() && panel()->supports(\Weblebby\Framework\Support\Features::themes()))
                        <x-weblebby::card padding>
                            <x-weblebby::card.title>@lang('Şablon')</x-weblebby::card.title>
                            <x-weblebby::form.group name="template">
                                <x-weblebby::form.select data-post-type="{{ $post::class }}">
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
                        <x-weblebby::form.image name="featured_image" :image="$post->getFirstMediaUrl('featured')" />
                    </x-weblebby::card>
                    <x-weblebby::card padding>
                        <x-weblebby::card.title>@lang('İçeriği sil')</x-weblebby::card.title>
                        <x-weblebby::button
                                type="button"
                                variant="red"
                                data-modal-open="#modal-delete-post"
                                :data-action="panel_route('posts.destroy', $post)"
                        >@lang('Kalıcı olarak sil')</x-weblebby::button>
                    </x-weblebby::card>
                </div>
            </div>
            <x-weblebby::form.sticky-submit />
        </x-weblebby::form>
    </x-weblebby::page>
    @push('after_scripts')
        <x-weblebby::modal.destroy id="modal-delete-post" :title="__('Sil: :name', ['name' => $post->title])" />
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
