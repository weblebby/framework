<?php

namespace Feadmin\Http\Middleware;

use Closure;
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

        Localization::group('panel', [
            'title' => t('Panel', 'panel'),
            'description' => t('Paneldeki metinleri çevirin.', 'panel'),
        ]);

        return $next($request);
    }
}
