<?php

namespace Weblebby\Framework\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Weblebby\Framework\Abstracts\Extension\Extension as ExtensionAbstract;
use Weblebby\Framework\Console\Commands\FetchCurrencyRates;
use Weblebby\Framework\Console\Commands\InstallWeblebby;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Facades\PostModels;
use Weblebby\Framework\Models\User;

class WeblebbyServiceProvider extends ServiceProvider
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
            \Weblebby\Framework\Managers\ExtensionManager::class,
            \Weblebby\Framework\Managers\InjectionManager::class,
            \Weblebby\Framework\Managers\NavigationLinkableManager::class,
            \Weblebby\Framework\Managers\PreferenceManager::class,
            \Weblebby\Framework\Managers\ThemeManager::class,
            \Weblebby\Framework\Managers\LogManager::class,
            \Weblebby\Framework\Support\CurrencyRate::class,
            \Weblebby\Framework\Support\HtmlSanitizer::class,
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
        $this->loadViewsFrom(dirname(__DIR__).'/../resources/views', 'weblebby');

        Blade::directive('hook', function ($expression) {
            return "<?php echo \Weblebby\Framework\Facades\Injection::render($expression); ?>";
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
            InstallWeblebby::class,
            FetchCurrencyRates::class,
        ]);
    }

    private function bootPostModels(): void
    {
        PostModels::register([
            \Weblebby\Framework\Models\Post::class,
            \Weblebby\Framework\Models\Page::class,
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
        $path = public_path('weblebby');

        if (! File::isDirectory($path)) {
            symlink(dirname(__DIR__).'/../public', $path);
        }
    }
}
