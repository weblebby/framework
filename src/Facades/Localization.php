<?php

namespace Feadmin\Facades;

use Feadmin\Managers\LocalizationManager;
use Illuminate\Support\Collection;
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
 * @method static Collection getSupportedLocales()
 * @method static Collection getAllLocales()
 * @method static object getLocale(string $code)
 * @method static array getTranslations(string $code = null, array $filters = [])
 * @method static Collection getRemainingLocales()
 * @method static string currencyCode(string $code)
 * @method static string display(string $code)
 * @method static string date(\Carbon\Carbon $date)
 * @method static void setCurrentLocale(object $locale)
 * @method static object|null getPreferredLocale(array $priorities)
 *
 * @see LocalizationManager
 */
class Localization extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LocalizationManager::class;
    }
}
