<?php

namespace Weblebby\Framework\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Weblebby\Framework\Facades\Extension;

class ExtensionPreferenceController extends Controller
{
    public function index(string $namespace): RedirectResponse
    {
        $preferences = panel()->preference($namespace)->get();

        return to_panel_route('extensions.preferences.show', [$namespace, key($preferences)]);
    }

    public function show(string $name, string $bag): View
    {
        $extension = Extension::findByNameOrFail($name);

        $preferences = panel()->preference($extension->name())->get();
        $foundBag = $preferences[$bag] ?? null;

        abort_if(is_null($foundBag), 404);

        seo()->title("{$extension->pluralTitle()}: {$foundBag['title']}");

        return view('weblebby::user.extensions.preferences.show', [
            'extension' => $extension,
            'selectedBag' => $bag,
            'preferences' => $preferences,
        ]);
    }

    public function update(Request $request, string $namespace, string $bag): RedirectResponse
    {
        return (new PreferenceController())->update($request, $namespace, $bag);
    }
}
