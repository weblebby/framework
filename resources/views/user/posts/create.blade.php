<x-feadmin::layouts.panel>
    <x-feadmin::page id="post" class="fd-mx-auto">
        <x-feadmin::page.head :back="panel_route('posts.index', ['type' => $postable::getModelName()])">
            <x-feadmin::page.title>@lang(':name oluşturun', ['name' => $postable::getSingularName()])</x-feadmin::page.title>
        </x-feadmin::page.head>
        <x-feadmin::form :action="panel_route('posts.store')" enctype="multipart/form-data">
            <input type="hidden" name="postable" value="{{ $postable::getModelName() }}">
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
                                        <x-feadmin::form.field :field="$field" />
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
                    @if ($categoryTax = $postable::getTaxonomyFor('category'))
                        <x-feadmin::card padding>
                            <x-feadmin::card.title>@lang('Kategori')</x-feadmin::card.title>
                            <x-feadmin::taxonomies
                                    for="category"
                                    :taxonomies="$categories"
                                    :taxonomyItem="$categoryTax"
                            />
                        </x-feadmin::card>
                    @endif
                    @if ($tagTax = $postable::getTaxonomyFor('tag'))
                        <x-feadmin::card padding>
                            <x-feadmin::card.title>@lang('Etiketler')</x-feadmin::card.title>
                            <x-feadmin::form.tagify :options="[
                                'source' => panel_api_route('taxonomies.index', $tagTax->name()),
                                'map' => ['value' => 'taxonomy_id', 'label' => 'title'],
                                'name' => sprintf('taxonomies[%s][]', $tagTax->name()),
                            ]" />
                        </x-feadmin::card>
                    @endif
                    @if ($postable::doesSupportTemplates())
                        <x-feadmin::card padding>
                            <x-feadmin::card.title>@lang('Şablon')</x-feadmin::card.title>
                            <x-feadmin::form.group name="template">
                                <x-feadmin::form.select data-post-type="{{ $postable::class }}">
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
                        <x-feadmin::form.image name="featured_image" />
                    </x-feadmin::card>
                </div>
            </div>
            <x-feadmin::form.sticky-submit />
        </x-feadmin::form>
    </x-feadmin::page>
    <x-slot:scripts>
        <x-feadmin::tabs.template />
        <script>
          window.Feadmin.Theme = {
            postFieldsUrl: @json(panel_route('themes.templates.post-fields', [':theme', ':template']))
          };
        </script>
        @vite('resources/js/pages/post/post.js', 'feadmin')
    </x-slot:scripts>
</x-feadmin::layouts.panel>