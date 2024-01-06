<?php

namespace Feadmin\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Arr::macro('after', function (array $array, $search, $value) {
            $position = array_search($search, $array, true);

            if ($position === false) {
                return $array;
            }

            return array_merge(
                array_slice($array, 0, $position + 1),
                Arr::wrap($value),
                array_slice($array, $position + 1)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
