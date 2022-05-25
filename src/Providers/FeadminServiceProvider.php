<?php

namespace Feadmin\Providers;

use Feadmin\Console\Commands\InstallFeadmin;
use Feadmin\Console\Commands\MigrateExtension;
use Feadmin\Facades\Extension;
use Feadmin\Facades\Localization;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Exceptions\SupportedLocalesNotDefined;

class FeadminServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(FortifyServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootPublishes();
            $this->bootCommands();
        } else {
            $this->bootViews();
            $this->bootLocalization();
            $this->bootGates();
        }

        Extension::start();
    }

    private function bootViews(): void
    {
        Paginator::defaultView('feadmin::vendor.pagination.tailwind');
        Paginator::defaultSimpleView('feadmin::vendor.pagination.simple-tailwind');

        $this->loadViewsFrom(dirname(__DIR__) . '/../resources/views', 'feadmin');

        Blade::directive('feinject', function ($expression) {
            return "<?php echo \Feadmin\Facades\Injection::render($expression); ?>";
        });
    }

    private function bootLocalization(): void
    {
        $allLocales = Localization::getAllLocales();
        $availableLocaleCodes = Localization::getAvailableLocales()->pluck('code')->toArray();
        $supportedLocales = $allLocales->whereIn('code', $availableLocaleCodes)->toArray();

        if (count($supportedLocales) <= 0) {
            throw new SupportedLocalesNotDefined('No supported locales found.');
        }

        config([
            'translatable.locales' => $availableLocaleCodes,
            'translatable.use_fallback' => true,
            'translatable.fallback_locale' => null,
            'laravellocalization.supportedLocales' => $supportedLocales,
        ]);
    }

    private function bootGates(): void
    {
        Gate::before(function ($user) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }

    private function bootPublishes(): void
    {
        $this->publishes([
            dirname(__DIR__) . '/../resources/views' => resource_path('views/vendor/feadmin'),
        ], ['feadmin-views', 'views']);

        $this->publishes([
            dirname(__DIR__) . '/../public' => public_path('vendor/feadmin'),
        ], ['feadmin-public', 'public']);

        $this->publishes([
            dirname(__DIR__) . '/../database/migrations' => database_path('migrations'),
        ], ['feadmin-migrations', 'migrations']);

        $this->publishes([
            dirname(__DIR__) . '/../config/feadmin.php' => config_path('feadmin.php'),
        ], ['feadmin-config', 'config']);
    }

    public function bootCommands(): void
    {
        $this->commands([
            InstallFeadmin::class,
            MigrateExtension::class,
        ]);
    }
}
