<?php

namespace Feadmin\Http\Middleware;

use Closure;
use Feadmin\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanUserAccessPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = $request->user();
        abort_if(is_null($user) || ! $user->canAccessPanel(panel()->name()), 403);

        return $next($request);
    }
}