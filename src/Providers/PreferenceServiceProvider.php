<?php

namespace Feadmin\Providers;

use Feadmin\Facades\Preference;
use Feadmin\Items\Field\FieldItem;
use Illuminate\Support\ServiceProvider;

class PreferenceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->bootForPosts();
    }

    private function bootForPosts(): void
    {
        Preference::create('default', 'slugs')
            ->addMany([
                FieldItem::paragraph(
                    __('Sayfa adreslerini bu bölümden ayarlayabilirsiniz.')
                ),

                FieldItem::text('post')
                    ->translatable()
                    ->label(__('Yazı adresi'))
                    ->rules(['required', 'string', 'max:191'])
                    ->default('makale'),

                FieldItem::text('page')
                    ->translatable()
                    ->label(__('Sayfa adresi'))
                    ->rules(['required', 'string', 'max:191'])
                    ->default('sayfa'),
            ]);
    }
}
