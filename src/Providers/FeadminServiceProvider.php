<?php

namespace Feadmin\Providers;

use Feadmin\Abstracts\Extension\Extension as ExtensionAbstract;
use Feadmin\Console\Commands\InstallFeadmin;
use Feadmin\Console\Commands\MigrateExtension;
use Feadmin\Facades\Extension;
use Feadmin\Facades\PostModels;
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
        $this->app->register(MacroServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(FortifyServiceProvider::class);
        $this->app->register(PreferenceServiceProvider::class);

        $singletons = [
            \Feadmin\Managers\ExtensionManager::class,
            \Feadmin\Managers\InjectionManager::class,
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
            $this->bootGates();
            $this->bootPostModels();
            $this->observeExtensions();
            $this->setPathsForTranslationFinder();
        }
    }

    private function bootViews(): void
    {
        $this->loadViewsFrom(dirname(__DIR__).'/../resources/views', 'feadmin');

        Blade::directive('feinject', function ($expression) {
            return "<?php echo \Feadmin\Facades\Injection::render($expression); ?>";
        });
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
            dirname(__DIR__).'/../resources/views' => resource_path('views/vendor/feadmin'),
        ], ['feadmin-views', 'views']);

        $this->publishes([
            dirname(__DIR__).'/../public' => public_path('vendor/feadmin'),
        ], ['feadmin-public', 'public']);

        $this->publishes([
            dirname(__DIR__).'/../database/migrations' => database_path('migrations'),
        ], ['feadmin-migrations', 'migrations']);
    }

    private function bootCommands(): void
    {
        $this->commands([
            InstallFeadmin::class,
            MigrateExtension::class,
        ]);
    }

    private function bootPostModels(): void
    {
        PostModels::register([
            \Feadmin\Models\Post::class,
            \Feadmin\Models\Page::class,
        ]);
    }

    private function observeExtensions(): void
    {
        Extension::get()->each(function (ExtensionAbstract $extension) {
            $extension->observer()?->boot();
        });
    }

    private function setPathsForTranslationFinder(): void
    {
        if (Extension::has('multilingual')) {
            \Weblebby\Extensions\Multilingual\Services\TranslationFinderService::addDirectories([
                dirname(__DIR__),
                dirname(__DIR__).'/../resources',
                dirname(__DIR__).'/../routes',
            ]);
        }
    }
}
