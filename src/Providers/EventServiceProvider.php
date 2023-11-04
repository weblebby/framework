<?php

namespace Feadmin\Providers;

use App\Models\User;
use Feadmin\Listeners\UpdateLocale;
use Feadmin\Observers\UserObserver;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected array $listen = [
        LocaleUpdated::class => [
            UpdateLocale::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}
