<?php

namespace Feadmin\Providers;

use Feadmin\Abstracts\Extension\Extension as ExtensionAbstract;
use Feadmin\Console\Commands\FetchCurrencyRates;
use Feadmin\Console\Commands\InstallFeadmin;
use Feadmin\Facades\Extension;
use Feadmin\Facades\PostModels;
use Feadmin\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
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
            \Feadmin\Managers\LogManager::class,
            \Feadmin\Support\CurrencyRate::class,
            \Feadmin\Support\HtmlSanitizer::class,
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
            $this->bootCommands();
        } else {
            $this->registerExtensions();
            $this->ensureBuildDirectoryExists();
            $this->bootViews();
            $this->bootGates();
            $this->bootPostModels();
            $this->setPathsForTranslationFinder();
            $this->bootExtensions();
        }

        $this->loadMigrationsFrom(dirname(__DIR__).'/../database/migrations');
    }

    private function bootViews(): void
    {
        $this->loadViewsFrom(dirname(__DIR__).'/../resources/views', 'feadmin');

        Blade::directive('hook', function ($expression) {
            return "<?php echo \Feadmin\Facades\Injection::render($expression); ?>";
        });
    }

    private function bootGates(): void
    {
        Gate::before(function (User $user) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }

    private function bootCommands(): void
    {
        $this->commands([
            InstallFeadmin::class,
            FetchCurrencyRates::class,
        ]);
    }

    private function bootPostModels(): void
    {
        PostModels::register([
            \Feadmin\Models\Post::class,
            \Feadmin\Models\Page::class,
        ]);
    }

    private function registerExtensions(): void
    {
        Extension::get()->each(function (ExtensionAbstract $extension) {
            $extension->observer()?->register();
        });
    }

    private function bootExtensions(): void
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
            ], panel());
        }
    }

    private function ensureBuildDirectoryExists(): void
    {
        $path = public_path('feadmin');

        if (! File::isDirectory($path)) {
            symlink(dirname(__DIR__).'/../public', $path);
        }
    }
}
