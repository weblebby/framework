<?php

namespace Feadmin\Facades;

use App\Models\User;
use Feadmin\Managers\LogManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Feadmin\Models\Log store(string $action, array $payload = [], Model $loggable = null, User $user = null)
 *
 * @see LogManager
 */
class Log extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LogManager::class;
    }
}
