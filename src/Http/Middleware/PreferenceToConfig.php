<?php

namespace Weblebby\Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreferenceToConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        config([
            'app.name' => $siteName = preference('general->site_name'),
            'app.url' => preference('general->site_url'),
            'seo.app.name' => $siteName,
        ]);

        return $next($request);
    }
}
