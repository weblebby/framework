<?php

namespace Weblebby\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Weblebby\Framework\Facades\Extension;

class ExtensionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Extension::observeRegister();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Extension::observeBoot();
    }
}
