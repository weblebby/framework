<?php

namespace Weblebby\Framework\Providers;

use Illuminate\Support\ServiceProvider;

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
        //
    }
}
