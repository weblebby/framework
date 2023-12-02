<?php

namespace Feadmin\Http\Middleware;

use Closure;
use Feadmin\Facades\Localization;
use Feadmin\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserPreferredLocale
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

        $preferredLocale = $user?->locale?->code ?? Localization::getCurrentLocale()->code;
        app()->setLocale($preferredLocale);

        return $next($request);
    }
}
