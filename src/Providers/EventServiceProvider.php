<?php

namespace Feadmin\Providers;

use Feadmin\Listeners\DeleteOriginalMedia;
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
        ConversionHasBeenCompleted::class => [
            DeleteOriginalMedia::class,
        ],
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
