<?php

namespace Feadmin\Http\Controllers\User;

use Core\Facades\PreferenceManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtensionPreferenceController extends Controller
{
    public function index(string $namespace): RedirectResponse
    {
        $preferences = PreferenceManager::namespaces($namespace);

        if (is_null($preferences)) {
            abort(404);
        }

        return redirect()->route('admin::extensions.preferences.show', [$namespace, key($preferences)]);
    }

    public function show(string $id, string $bag): View
    {
        $extension = extensions()->get()->firstWhere('id', $id);

        if (is_null($extension)) {
            abort(404);
        }

        $namespaces = PreferenceManager::namespaces($extension->id);
        $findedBag = $namespaces[$bag] ?? null;

        if (is_null($findedBag)) {
            abort(404);
        }

        seo()->title("{$extension->plural_title}: {$findedBag['title']}");

        return view('feadmin::user.extensions.preferences.show', [
            'extension' => $extension,
            'selectedBag' => $bag,
            'namespaces' => $namespaces,
        ]);
    }

    public function update(Request $request, string $id, string $bag): RedirectResponse
    {
        return (new PreferenceController())->update($request, $bag, $id);
    }
}
