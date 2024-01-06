<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Preference;
use Feadmin\Http\Requests\User\UpdatePreferenceRequest;
use Feadmin\Items\Field\CodeEditorFieldItem;
use Feadmin\Services\FieldInputService;
use Illuminate\Http\RedirectResponse;
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
        $bagHook = panel()->preference(self::NAMESPACE);
        $bags = $bagHook->get();

        abort_if(is_null($selectedBag = $bags[$bag] ?? null), 404);

        $fields = $bagHook->fields($bag);
        $isCodeEditorNeeded = $fields->hasAnyTypeOf(CodeEditorFieldItem::class);

        seo()->title($selectedBag['title']);

        return view('feadmin::user.preferences.show', [
            ...compact('bags', 'fields', 'isCodeEditorNeeded'),
            'selectedBagId' => $bag,
            'namespace' => self::NAMESPACE,
        ]);
    }

    public function update(
        UpdatePreferenceRequest $request,
        FieldInputService $fieldInputService,
        string $namespace,
        string $bag
    ): RedirectResponse {
        $fields = Preference::fields($namespace, $bag);

        $validatedFieldValues = $fieldInputService->getFieldValues($fields, $request->validated());
        $validatedFieldValues = $fieldInputService->getDottedFieldValues($validatedFieldValues);

        preference($validatedFieldValues, options: [
            'deleted_fields' => $validated['_deleted_fields'] ?? [],
            'reordered_fields' => $validated['_reordered_fields'] ?? [],
        ]);

        return back()->with('message', __('Ayarlar kaydedildi'));
    }
}
