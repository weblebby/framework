<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThemeTemplatePostFieldController extends Controller
{
    public function __invoke(Request $request, string $theme, string $template): JsonResponse
    {
        $theme = Theme::find($theme);
        abort_if(is_null($theme), 404);

        $template = $theme->templatesFor($request->type)->firstWhere('name', $template);
        abort_if(is_null($template), 404);

        $tabs = collect($template->sections()->toArray())
            ->map(function ($section, $tab) {
                return [
                    'id' => $tab,
                    'title' => $section['title'],
                    'fields' => view('feadmin::user.themes.templates.post-fields', ['fields' => $section['fields']])->render(),
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'tabs' => $tabs,
        ]);
    }
}
