<?php

namespace Feadmin\Providers;

use Feadmin\Console\Commands\MigrateCommand;
use Feadmin\Facades\Localization;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class FeadminServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootCommands();

            return;
        }

        $this->bootViews();
        $this->bootLocalization();
        $this->bootGates();
    }

    private function bootViews(): void
    {
        $this->loadViewsFrom(dirname(__DIR__) . '/../resources/views', 'feadmin');

        $this->publishes([
            dirname(__DIR__) . '/../resources/views' => resource_path('views/vendor/feadmin'),
        ], 'feadmin-views');
    }

    private function bootLocalization(): void
    {
        Blade::directive('t', function ($expression) {
            return "<?php echo t({$expression}) ?>";
        });

        $this->app->setLocale(Localization::getCurrentLocale()->code);

        $allLocales = Localization::getAllLocales();
        $availableLocaleCodes = Localization::getAvailableLocales()->pluck('code')->toArray();

        config([
            'translatable.locales' => $availableLocaleCodes,
            'laravellocalization.supportedLocales' => $allLocales
                ->whereIn('code', $availableLocaleCodes)
                ->toArray()
        ]);

        Localization::group('admin', [
            'title' => t('Admin paneli', 'admin'),
            'description' => t('Admin panelindeki metinleri çevirin.', 'admin'),
        ]);

        Localization::group('routes', [
            'title' => t('Sayfa adresleri', 'admin'),
            'description' => t('Sitedeki tüm modüllerin bağlantı yapılarını çevirin', 'admin')
        ]);
    }

    private function bootCommands(): void
    {
        $this->commands([
            MigrateCommand::class,
        ]);
    }

    private function bootGates(): void
    {
        Gate::before(function ($user) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
