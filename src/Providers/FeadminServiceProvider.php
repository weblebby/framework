<?php

namespace Feadmin\Providers;

use Astrotomic\Translatable\Locales;
use Feadmin\Console\Commands\InstallFeadmin;
use Feadmin\Console\Commands\MigrateExtension;
use Feadmin\Facades\Localization;
use Feadmin\Facades\PostModels;
use Feadmin\Facades\Preference;
use Feadmin\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class FeadminServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(FortifyServiceProvider::class);
        $this->app->register(PreferenceServiceProvider::class);

        $singletons = [
            \Feadmin\Managers\ExtensionManager::class,
            \Feadmin\Managers\InjectionManager::class,
            \Feadmin\Managers\LocalizationManager::class,
            \Feadmin\Managers\NavigationLinkableManager::class,
            \Feadmin\Managers\PreferenceManager::class,
            \Feadmin\Managers\ThemeManager::class,
        ];

        foreach ($singletons as $singleton) {
            $this->app->singleton($singleton);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootPublishes();
            $this->bootCommands();
        } else {
            $this->bootViews();
            $this->bootLocalization();
            $this->bootGates();
            $this->bootPostModels();
        }

        app(Locales::class)->load();
        Preference::loadPreferences();
    }

    private function bootViews(): void
    {
        $this->loadViewsFrom(dirname(__DIR__) . '/../resources/views', 'feadmin');

        Blade::directive('feinject', function ($expression) {
            return "<?php echo \Feadmin\Facades\Injection::render($expression); ?>";
        });
    }

    private function bootLocalization(): void
    {
        config([
            'translatable.locales' => Localization::getSupportedLocales()->pluck('code')->toArray(),
            'translatable.use_fallback' => true,
            'translatable.fallback_locale' => null,
        ]);
    }

    private function bootGates(): void
    {
        Gate::before(function (User $user) {
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
    }

    public function bootCommands(): void
    {
        $this->commands([
            InstallFeadmin::class,
            MigrateExtension::class,
        ]);
    }

    public function bootPostModels(): void
    {
        PostModels::register([
            \Feadmin\Models\Post::class,
            \Feadmin\Models\Page::class,
        ]);
    }
}
