<?php

namespace Weblebby\Framework\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Weblebby\Framework\Abstracts\Extension\Extension as ExtensionAbstract;
use Weblebby\Framework\Abstracts\Theme\Theme as ThemeAbstract;
use Weblebby\Framework\Console\Commands\FetchCurrencyRates;
use Weblebby\Framework\Console\Commands\InstallWeblebby;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Facades\PostModels;
use Weblebby\Framework\Facades\Preference;
use Weblebby\Framework\Facades\Theme;
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
            \Weblebby\Framework\Managers\NavigationItemsManager::class,
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
            $this->ensureBuildsExists();
            $this->bootPreferences();
            $this->bootViews();
            $this->bootGates();
            $this->bootPostModels();
            $this->setPathsForTranslationFinder();

            Extension::observeAfterPanelBoot();
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

    private function ensureBuildsExists(): void
    {
        $this->publishBuild(
            sourcePath: dirname(__DIR__).'/../public',
            targetPath: public_path(panel_build_dir())
        );

        Theme::get()->each(function (ThemeAbstract $theme) {
            $this->publishBuild(
                sourcePath: theme()->path('public'),
                targetPath: public_path(sprintf('%s/%s', themes_build_dir(), $theme->name()))
            );
        });

        Extension::get()->each(function (ExtensionAbstract $extension) {
            $this->publishBuild(
                sourcePath: $extension->path('public'),
                targetPath: public_path(sprintf('%s/%s', extensions_build_dir(), $extension->name()))
            );
        });
    }

    private function bootPreferences(): void
    {
        foreach (theme()->preferences()->toArray() as $bag => $section) {
            Preference::create(theme()->namespace(), $bag)->addMany($section['fields']);
        }
    }

    private function publishBuild(string $sourcePath, string $targetPath): void
    {
        if (! File::exists($sourcePath)) {
            return;
        }

        if (File::exists($targetPath)) {
            return;
        }

        File::ensureDirectoryExists(dirname($targetPath));

        symlink($sourcePath, $targetPath);
    }
}
