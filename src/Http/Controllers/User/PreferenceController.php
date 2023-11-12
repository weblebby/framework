<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Preference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreferenceController extends Controller
{
    const NAMESPACE = 'default';

    public function index(): RedirectResponse
    {
        return to_panel_route(
            'preferences.show',
            key(panel()->preference(self::NAMESPACE)->get())
        );
    }

    public function show(string $bag): View
    {
        $foundBag = panel()->preference(self::NAMESPACE)->get()[$bag] ?? null;
        abort_if(is_null($foundBag), 404);

        seo()->title($foundBag['title']);

        return view('feadmin::user.preferences.show', [
            'selectedBag' => $bag,
            'namespace' => self::NAMESPACE,
        ]);
    }

    public function update(Request $request, string $namespace, string $bag): RedirectResponse
    {
        $fields = Preference::fields($namespace, $bag)
            ->filter(fn($field) => $field['type']->isEditable())
            ->values();

        $fieldsForValidation = Preference::fieldsForValidation($namespace, $bag);

        $validated = $request->validate(
            $fieldsForValidation['rules'],
            attributes: $fieldsForValidation['attributes'],
        );

        $uploadables = [];

        foreach ($fields as $field) {
            $value = $validated[$field['name']] ?? null;

            if (is_null($value)) {
                continue;
            }

            if ($field['type']->isUploadable()) {
                $uploadables[$field['name']] = $value;
            }
        }

        foreach ($uploadables as $key => $uploadable) {
            preference([$key => true])[0]
                ->addMedia($uploadable)
                ->toMediaCollection();

            unset($validated[$key]);
        }

        $a = preference($validated);
        dd($a);

        return back()->with('message', __('Ayarlar kaydedildi'));
    }
}
