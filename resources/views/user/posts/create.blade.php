<x-feadmin::layouts.panel>
    <x-feadmin::page id="post" class="fd-mx-auto">
        <x-feadmin::page.head :back="panel_route('posts.index')">
            <x-feadmin::page.title>@lang('Yazı oluşturun')</x-feadmin::page.title>
            <x-feadmin::page.subtitle>@lang('Yeni bir yazı oluşturun')</x-feadmin::page.subtitle>
        </x-feadmin::page.head>
        <x-feadmin::form :action="panel_route('posts.store')" enctype="multipart/form-data">
            <div class="fd-flex fd-gap-3">
                <div class="fd-w-2/3 fd-space-y-2">
                    <x-feadmin::form.group name="title">
                        <x-feadmin::form.input :placeholder="__('Başlık')" autofocus />
                    </x-feadmin::form.group>
                    <x-feadmin::form.group name="content">
                        <x-feadmin::form.textarea :placeholder="__('İçerik')" data-ckeditor />
                    </x-feadmin::form.group>
                </div>
                <div class="fd-flex-1 fd-space-y-3">
                    <x-feadmin::card padding>
                        <x-feadmin::card.title>@lang('Durum')</x-feadmin::card.title>
                        <x-feadmin::form.group name="is_published">
                            <x-feadmin::form.checkbox :label="__('Yayında')" :default="true" />
                        </x-feadmin::form.group>
                    </x-feadmin::card>
                    <x-feadmin::card padding>
                        <x-feadmin::card.title>@lang('SEO')</x-feadmin::card.title>
                        <div class="fd-space-y-3">
                            <x-feadmin::form.group name="slug">
                                <x-feadmin::form.label>@lang('URL')</x-feadmin::form.label>
                                <div class="fd-flex fd-items-center fd-gap-2">
                                    <span class="fd-text-sm fd-text-zinc-500">{{ route('posts.show', '') }}/</span>
                                    <x-feadmin::form.input class="fd-flex-1" />
                                </div>
                            </x-feadmin::form.group>
                            <x-feadmin::form.group name="meta_title">
                                <x-feadmin::form.label>@lang('Meta başlığı')</x-feadmin::form.label>
                                <x-feadmin::form.input />
                                <x-feadmin::form.hint>@lang('Arama motorlarında görünecek sayfa başlığını buradan değiştirebilirsiniz.')</x-feadmin::form.hint>
                            </x-feadmin::form.group>
                            <x-feadmin::form.group name="meta_description">
                                <x-feadmin::form.label>@lang('Meta açıklaması')</x-feadmin::form.label>
                                <x-feadmin::form.textarea rows="3" />
                            </x-feadmin::form.group>
                        </div>
                    </x-feadmin::card>
                    <x-feadmin::card padding>
                        <x-feadmin::card.title>@lang('Önerilen görsel')</x-feadmin::card.title>
                        <x-feadmin::form.image name="featured_image" />
                    </x-feadmin::card>
                </div>
            </div>
            <x-feadmin::form.sticky-submit />
        </x-feadmin::form>
    </x-feadmin::page>
</x-feadmin::layouts.panel>
