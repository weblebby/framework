<?php

namespace Weblebby\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Weblebby\Framework\Facades\Preference;
use Weblebby\Framework\Items\Field\FieldItem;

class PreferenceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
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
