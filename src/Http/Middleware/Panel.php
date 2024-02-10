<?php

namespace Weblebby\Framework\Http\Middleware;

use Closure;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;
use Weblebby\Framework\Facades\Panel as PanelManager;
use Weblebby\Framework\Facades\PostModels;

class Panel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        foreach (PostModels::get() as $model) {
            $model->register();
        }

        ResetPassword::$createUrlCallback = function ($notifiable, $token) {
            return url(panel()->route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        };

        Paginator::defaultView('weblebby::vendor.pagination.tailwind');
        Paginator::defaultSimpleView('weblebby::vendor.pagination.simple-tailwind');

        if (PanelManager::getExtensionPanel() === panel()) {
            $preference = panel()->preference(theme()->namespace());

            foreach (theme()->preferences()->toArray() as $bag => $section) {
                $preference->withBag($bag, $section['title']);
            }
        }

        return $next($request);
    }
}
