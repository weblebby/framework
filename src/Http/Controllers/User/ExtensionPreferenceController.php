<?php

namespace Feadmin\Http\Controllers\User;

use Core\Facades\PreferenceManager;
use App\Http\Controllers\Controller;
use Feadmin\Facades\Extension;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtensionPreferenceController extends Controller
{
    public function index(string $namespace): RedirectResponse
    {
        $preferences = panel()->preference($namespace)->get();

        if (is_null($preferences)) {
            abort(404);
        }

        return to_panel_route('extensions.preferences.show', [$namespace, key($preferences)]);
    }

    public function show(string $id, string $bag): View
    {
        $extension = Extension::get()->firstWhere('id', $id);

        if (is_null($extension)) {
            abort(404);
        }

        $preferences = panel()->preference($extension->id)->get();
        $findedBag = $preferences[$bag] ?? null;

        if (is_null($findedBag)) {
            abort(404);
        }

        seo()->title("{$extension->plural_title}: {$findedBag['title']}");

        return view('feadmin::user.extensions.preferences.show', [
            'extension' => $extension,
            'selectedBag' => $bag,
            'preferences' => $preferences,
        ]);
    }

    public function update(Request $request, string $id, string $bag): RedirectResponse
    {
        return (new PreferenceController())->update($request, $bag, $id);
    }
}
