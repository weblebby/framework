<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Feadmin\Hooks\Preference hook()
 * @method static mixed get(string $rawKey, mixed $default = null)
 * @method static array set(array $data)
 * 
 * @see \Feadmin\Services\PreferenceService
 */
class Preference extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\PreferenceService::class;
    }
}
