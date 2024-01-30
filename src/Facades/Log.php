<?php

namespace Weblebby\Framework\Facades;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Weblebby\Framework\Managers\LogManager;

/**
 * @method static \Weblebby\Framework\Models\Log store(string $action, array $payload = [], Model $loggable = null, User $user = null)
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
