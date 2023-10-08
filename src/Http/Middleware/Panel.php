<?php

namespace Feadmin\Http\Middleware;

use Closure;
use Feadmin\Extension as ExtensionItem;
use Feadmin\Facades\Extension;
use Feadmin\Facades\Localization;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

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
        $authorizedPanels = $request->user()->authorizedPanels();

        if ($authorizedPanels === false || (is_array($authorizedPanels) && !in_array(panel()->name(), $authorizedPanels))) {
            abort(403);
        }

        app()->setLocale($request->user()?->locale?->code ?? Localization::getCurrentLocale()->code);

        config([
            'app.name' => $siteName = preference('general->site_name'),
            'seo.app.name' => $siteName,
        ]);

        Extension::enabled()->each(fn (ExtensionItem $extension) => $extension->booted());

        Paginator::defaultView('feadmin::vendor.pagination.tailwind');
        Paginator::defaultSimpleView('feadmin::vendor.pagination.simple-tailwind');

        return $next($request);
    }
}
