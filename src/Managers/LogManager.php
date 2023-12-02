<?php

namespace Feadmin\Managers;

use App\Models\User;
use Feadmin\Models\Log;
use Illuminate\Database\Eloquent\Model;

class LogManager
{
    public function store(
        string $action,
        array $payload = [],
        Model $loggable = null,
        User $user = null
    ): Log {
        /** @var User $user */
        $user ??= auth()->user();

        /** @var Log $log */
        $log = Log::query()->make(['action' => $action, 'payload' => $payload]);

        $loggable && $log->loggable()->associate($loggable);
        $user && $log->user()->associate($user);

        return tap($log)->save();
    }
}
