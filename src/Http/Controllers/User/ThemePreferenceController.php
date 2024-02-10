<?php

namespace Weblebby\Framework\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Weblebby\Framework\Facades\Theme;

class ThemePreferenceController extends Controller
{
    public function index(Request $request, string $theme, ?string $bag = null): View|RedirectResponse
    {
        $theme = Theme::findOrFail($theme);

        if (is_null($bag)) {
            return to_panel_route('themes.preferences.index', [
                $theme->name(),
                key(panel()->preference($theme->namespace())->get()),
            ]);
        }

        $preferenceController = new PreferenceController(
            namespace: $theme->namespace(),
            route: 'themes.preferences.index',
            routeParams: [$theme->name()],
            pageTitle: __(':theme Teması', ['theme' => $theme->title()]),
            pageDescription: __('Tema ayarlarınızı buradan yönetin.')
        );

        return $preferenceController->show($request, $bag);
    }
}
