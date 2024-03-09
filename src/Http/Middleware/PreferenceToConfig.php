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
        $siteName = preference('general->site_name');
        $appUrl = preference('general->site_url');

        if ($siteName) {
            config([
                'app.name' => $siteName,
                'seo.app.name' => $siteName,
            ]);
        }

        if ($appUrl) {
            config([
                'app.url' => $appUrl,
            ]);
        }

        if (preference('email->host')) {
            config([
                'mail.mailers.smtp.host' => preference('email->host'),
                'mail.mailers.smtp.port' => preference('email->port'),
                'mail.mailers.smtp.username' => preference('email->username'),
                'mail.mailers.smtp.password' => preference('email->password'),
                'mail.mailers.smtp.encryption' => preference('email->encryption'),
                'mail.from.name' => preference('email->from_name'),
                'mail.from.address' => preference('email->from_address'),
            ]);
        }

        return $next($request);
    }
}
