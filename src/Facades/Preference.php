<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

class Preference extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\PreferenceService::class;
    }
}
