<x-feadmin::layouts.panel>
    <x-feadmin::page id="post" class="fd-mx-auto">
        <x-feadmin::page.head :back="panel_route('posts.index', ['type' => $post::getModelName()])">
            <x-feadmin::page.title>@lang('Düzenle: :name', ['name' => $post->title])</x-feadmin::page.title>
        </x-feadmin::page.head>
        <x-feadmin::form
                :bind="$post"
                :action="panel_route('posts.update', $post)"
                method="PUT"
                enctype="multipart/form-data"
        >
            <div class="fd-flex fd-gap-3">
                <div class="fd-w-2/3 fd-space-y-2">
                    <x-feadmin::form.group name="title">
                        <x-feadmin::form.input :placeholder="__('Başlık')" autofocus />
                    </x-feadmin::form.group>
                    <x-feadmin::form.group name="content">
                        <x-feadmin::form.textarea :placeholder="__('İçerik')" data-ckeditor />
                    </x-feadmin::form.group>
                    <x-feadmin::tabs container="post" :default="array_keys($sections)[0] ?? null">
                        <x-feadmin::tabs.header>
                            @foreach ($sections as $id => $section)
                                <x-feadmin::tabs.button :id="$id">{{ $section['title'] }}</x-feadmin::tabs.button>
                            @endforeach
                        </x-feadmin::tabs.header>
                        @foreach($sections as $id => $section)
                            <x-feadmin::tabs.content :for="$id">
                                <div class="fd-space-y-3">
                                    @foreach ($section['fields'] as $field)
                                        <x-feadmin::form.field
                                                :field="$field"
                                                :default="$metafields[\Illuminate\Support\Str::replaceFirst('fields.', '', $field['key'])] ?? null"
                                        />
                                    @endforeach
                                </div>
                            </x-feadmin::tabs.content>
                        @endforeach
                    </x-feadmin::tabs>
                </div>
                <div class="fd-flex-1 fd-space-y-3">
                    <x-feadmin::card padding>
                        <x-feadmin::card.title>@lang('Durum')</x-feadmin::card.title>
                        <x-feadmin::form.group name="status">
                            <x-feadmin::form.select>
                                @foreach (\Feadmin\Enums\PostStatusEnum::cases() as $status)
                                    <x-feadmin::form.option
                                            value="{{ $status->value }}"
                                            :data-hint="$status->hint()">{{ $status->label() }}</x-feadmin::form.option>
                                @endforeach
                            </x-feadmin::form.select>
                            <x-feadmin::form.hint data-status-hint></x-feadmin::form.hint>
                        </x-feadmin::form.group>
                    </x-feadmin::card>
                    @if ($categoryTax = $post::getTaxonomyFor('category'))
                        <x-feadmin::card padding>
                            <x-feadmin::card.title>@lang('Kategori')</x-feadmin::card.title>
                            <x-feadmin::taxonomies
                                    for="category"
                                    :primary="$post->primaryTaxonomy"
                                    :taxonomies="$categories"
                                    :taxonomyItem="$categoryTax"
                            />
                        </x-feadmin::card>
                    @endif
                    @if ($tagTax = $post::getTaxonomyFor('tag'))
                        <x-feadmin::card padding>
                            <x-feadmin::card.title>@lang('Etiketler')</x-feadmin::card.title>
                            <x-feadmin::form.tagify
                                    :value="$post->getTaxonomiesFor($tagTax->name())->pluck('term.title')"
                                    :options="[
                                        'source' => panel_api_route('taxonomies.index', $tagTax->name()),
                                        'map' => ['value' => 'taxonomy_id', 'label' => 'title'],
                                        'name' => sprintf('taxonomies[%s][]', $tagTax->name()),
                                    ]"
                            />
                        </x-feadmin::card>
                    @endif
                    @if ($post::doesSupportTemplates())
                        <x-feadmin::card padding>
                            <x-feadmin::card.title>@lang('Şablon')</x-feadmin::card.title>
                            <x-feadmin::form.group name="template">
                                <x-feadmin::form.select data-post-type="{{ $post::class }}">
                                    <x-feadmin::form.option value="">@lang('Varsayılan')</x-feadmin::form.option>
                                    @foreach ($templates as $template)
                                            <?php /** @var \Feadmin\Abstracts\Theme\Template $template */ ?>
                                        <x-feadmin::form.option
                                                :value="$template->name()">{{ $template->title() }}</x-feadmin::form.option>
                                    @endforeach
                                </x-feadmin::form.select>
                                <x-feadmin::form.hint>@lang('Temanızın destekliği şablonları kullanarak sayfa düzenini değiştirebilirsiniz.')</x-feadmin::form.hint>
                            </x-feadmin::form.group>
                        </x-feadmin::card>
                    @endif
                    <x-feadmin::card padding>
                        <x-feadmin::card.title>@lang('Önerilen görsel')</x-feadmin::card.title>
                        <x-feadmin::form.image name="featured_image" :image="$post->getFirstMediaUrl('featured')" />
                    </x-feadmin::card>
                    <x-feadmin::card padding>
                        <x-feadmin::card.title>@lang('İçeriği sil')</x-feadmin::card.title>
                        <x-feadmin::button
                                type="button"
                                variant="red"
                                data-modal-open="#modal-delete-post"
                                :data-action="panel_route('posts.destroy', $post)"
                        >@lang('Kalıcı olarak sil')</x-feadmin::button>
                    </x-feadmin::card>
                </div>
            </div>
            <x-feadmin::form.sticky-submit />
        </x-feadmin::form>
    </x-feadmin::page>
    <x-slot:scripts>
        <x-feadmin::modal.destroy id="modal-delete-post" :title="__('Sil: :name', ['name' => $post->title])" />
        <x-feadmin::tabs.template />
        <script>
          window.Feadmin.Theme = {
            postFieldsUrl: @json(panel_route('themes.templates.post-fields', [':theme', ':template']))
          };
        </script>
        @vite('resources/js/pages/post/post.js', 'feadmin')
        @if($isCodeEditorNeeded)
            @vite('resources/js/code-editor.js', 'feadmin')
        @endif
    </x-slot:scripts>
</x-feadmin::layouts.panel>
