<?php

namespace Feadmin\Http\Middleware;

use Closure;
use Feadmin\Facades\Localization;
use Feadmin\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;

class Panel
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = $request->user();
        abort_if(is_null($user) || !$user->canAccessPanel(panel()->name()), 403);

        $preferredLocale = $user->locale?->code ?? Localization::getCurrentLocale()->code;
        app()->setLocale($preferredLocale);

        config([
            'app.name' => $siteName = preference('general->site_name'),
            'seo.app.name' => $siteName,
        ]);

        Paginator::defaultView('feadmin::vendor.pagination.tailwind');
        Paginator::defaultSimpleView('feadmin::vendor.pagination.simple-tailwind');

        return $next($request);
    }
}
