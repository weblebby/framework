<?php

namespace Weblebby\Framework\Managers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Weblebby\Framework\Models\Log;

class LogManager
{
    public function store(
        string $action,
        array $payload = [],
        ?Model $loggable = null,
        ?User $user = null
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
