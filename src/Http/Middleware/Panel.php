<?php

namespace Feadmin\Http\Middleware;

use Closure;
use Feadmin\Facades\PostModels;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;

class Panel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        config([
            'app.name' => $siteName = preference('general->site_name'),
            'seo.app.name' => $siteName,
        ]);

        Paginator::defaultView('feadmin::vendor.pagination.tailwind');
        Paginator::defaultSimpleView('feadmin::vendor.pagination.simple-tailwind');

        foreach (PostModels::get() as $model) {
            $model->register();
        }

        ResetPassword::$createUrlCallback = function ($notifiable, $token) {
            return url(panel()->route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        };

        return $next($request);
    }
}
