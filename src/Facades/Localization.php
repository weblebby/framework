<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void load()
 * @method static string|\Illuminate\Support\Collection get(string $key, string $group = 'default', array $replace = [], string $code = null)
 * @method static \Illuminate\Support\Collection getTranslations()
 * @method static \Illuminate\Support\Collection getTranslationsForClient()
 * @method static object getDefaultLocale()
 * @method static int getDefaultLocaleId()
 * @method static object getCurrentLocale()
 * @method static int getCurrentLocaleId()
 * @method static \Illuminate\Support\Collection getAvailableLocales()
 * @method static \Illuminate\Support\Collection getAllLocales()
 * @method static object getLocale(string $code)
 * @method static \Illuminate\Support\Collection getRemainingLocales()
 * @method static string display(string $code)
 * @method static string date(\Carbon\Carbon $date)
 * @method static void group(string $group, array $data)
 * @method static \Illuminate\Support\Collection groups()
 * @method static void setCurrentLocale(string $locale)
 */
class Localization extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\LocalizationService::class;
    }
}
