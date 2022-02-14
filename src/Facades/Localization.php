<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

class Localization extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\LocalizationService::class;
    }
}
