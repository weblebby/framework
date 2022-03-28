<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Feadmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreferenceController extends Controller
{
    private $namespace = 'default';

    public function index(): RedirectResponse
    {
        return redirect(
            Feadmin::panel()->route(
                'preferences.show',
                key(Feadmin::panel()->preferences($this->namespace)->get())
            )
        );
    }

    public function show(string $bag): View
    {
        $findedBag = Feadmin::panel()
            ->preferences($this->namespace)
            ->get()[$bag] ?? null;

        if (is_null($findedBag)) {
            abort(404);
        }

        seo()->title($findedBag['title']);

        return view('feadmin::user.preferences.show', [
            'selectedBag' => $bag,
            'namespace' => $this->namespace,
        ]);
    }

    public function update(Request $request, string $bag, string $namespace = null): RedirectResponse
    {
        $fields = Feadmin::panel()
            ->preferences($namespace ?: $this->namespace)
            ->fields($bag);

        $validated = $request->validate(
            $fields->pluck('rules', 'name')->toArray()
        );

        $uploadables = [];

        foreach ($fields as $field) {
            $value = $validated[$field['name']] ?? null;

            if (is_null($value)) {
                continue;
            }

            if ($field['type'] === 'image') {
                $uploadables[$field['name']] = $value;
            }
        }

        foreach ($uploadables as $key => $uploadable) {
            preference([$key => true])[0]
                ->addMedia($uploadable)
                ->toMediaCollection();

            unset($validated[$key]);
        }

        preference($validated);

        return back()->with('message', t('Ayarlar kaydedildi', 'admin'));
    }
}
