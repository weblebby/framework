<?php

namespace Feadmin\Http\Middleware;

use Closure;
use Feadmin\Extension as ExtensionItem;
use Feadmin\Facades\Extension;
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

        Extension::enabled()->each(fn (ExtensionItem $extension) => $extension->booted());

        return $next($request);
    }
}
