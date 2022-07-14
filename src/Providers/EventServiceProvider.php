<?php

namespace Feadmin\Providers;

use Feadmin\Listeners\DeleteOriginalMedia;
use App\Models\User;
use Feadmin\Listeners\UpdateLocale;
use Feadmin\Observers\UserObserver;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Spatie\MediaLibrary\Conversions\Events\ConversionHasBeenCompleted;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        LocaleUpdated::class => [
            UpdateLocale::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        User::class => [UserObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
