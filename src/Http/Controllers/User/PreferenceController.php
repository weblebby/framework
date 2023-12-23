<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Preference;
use Feadmin\Services\FieldInputService;
use Feadmin\Services\FieldValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

    public function update(
        Request                $request,
        FieldValidationService $fieldValidationService,
        FieldInputService      $fieldInputService,
        string                 $namespace,
        string                 $bag
    ): RedirectResponse
    {
        $fields = Preference::fields($namespace, $bag);

        $fieldValues = $fieldInputService->getFieldValues($fields, $request->all());
        $fieldsForValidation = $fieldValidationService->get($fields, $fieldValues);
        dd($fieldValues, $fieldsForValidation);

        $validated = $request->validate(
            $fieldsForValidation['rules'],
            attributes: $fieldsForValidation['attributes'],
        );

        $validated = Arr::dot($validated);

        preference($validated);

        return back()->with('message', __('Ayarlar kaydedildi'));
    }
}
