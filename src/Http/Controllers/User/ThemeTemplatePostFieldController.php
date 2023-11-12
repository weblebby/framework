<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Theme;
use Feadmin\Models\Post;
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

        $tabs = collect($template->tabs())
            ->map(function ($title, $tab) use ($template) {
                return [
                    'id' => $tab,
                    'title' => $title,
                    'fields' => view('feadmin::user.themes.templates.post-fields', ['fields' => $template->fields()[$tab] ?? []])->render(),
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'tabs' => $tabs,
        ]);
    }
}
