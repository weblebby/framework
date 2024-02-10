<?php

namespace Weblebby\Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteIsSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isSetupCompleted = filled(preference('default::core->setup'));
        $isOnSetupRoute = panel()->routeIs('setup.*');

        if (! $isSetupCompleted && ! $isOnSetupRoute) {
            abort(panel()->toRoute('setup.index'));
        }

        if ($isSetupCompleted && $isOnSetupRoute) {
            abort(panel()->toRoute('dashboard'));
        }

        return $next($request);
    }
}
