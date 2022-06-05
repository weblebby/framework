<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void load()
 * @method static void loadAllLocales()
 * @method static object getDefaultLocale()
 * @method static string getDefaultLocaleCode()
 * @method static int getDefaultLocaleId()
 * @method static object getCurrentLocale()
 * @method static string getCurrentLocaleCode()
 * @method static int getCurrentLocaleId()
 * @method static \Illuminate\Support\Collection getSupportedLocales()
 * @method static \Illuminate\Support\Collection getAllLocales()
 * @method static object getLocale(string $code)
 * @method static array getTranslations()
 * @method static \Illuminate\Support\Collection getRemainingLocales()
 * @method static string display(string $code)
 * @method static string date(\Carbon\Carbon $date)
 * @method static void setCurrentLocale(object $locale)
 */
class Localization extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\LocalizationService::class;
    }
}
