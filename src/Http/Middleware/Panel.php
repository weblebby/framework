<?php

namespace Feadmin\Http\Middleware;

use Closure;
use Feadmin\Extension;
use Feadmin\Facades\Extension as ExtensionService;
use Feadmin\Facades\Localization;
use Illuminate\Http\Request;

class Panel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Localization::setCurrentLocale(
            $request->user()?->locale?->code ?? Localization::getCurrentLocale()->code
        );

        config([
            'app.name' => $siteName = preference('general->site_name'),
            'seo.app.name' => $siteName,
        ]);

        ExtensionService::enabled()->each(fn (Extension $extension) => $extension->booted());

        return $next($request);
    }
}
