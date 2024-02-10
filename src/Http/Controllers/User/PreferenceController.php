<?php

namespace Weblebby\Framework\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Weblebby\Framework\Facades\Preference;
use Weblebby\Framework\Http\Requests\User\UpdatePreferenceRequest;
use Weblebby\Framework\Items\Field\CodeEditorFieldItem;
use Weblebby\Framework\Services\FieldInputService;

class PreferenceController extends Controller
{
    public function __construct(
        protected string $namespace = 'default',
        protected string $route = 'preferences.show',
        protected array $routeParams = [],
        protected ?string $pageTitle = null,
        protected ?string $pageDescription = null,
    ) {
        //
    }

    public function index(): RedirectResponse
    {
        return to_panel_route(
            'preferences.show',
            key(panel()->preference($this->namespace)->get())
        );
    }

    public function show(Request $request, string $bag): View
    {
        $bagHook = panel()->preference($this->namespace);
        $bags = $bagHook->get();

        abort_if(is_null($selectedBag = $bags[$bag] ?? null), 404);

        $fields = $bagHook->fields($bag, $request->input('_locale'));
        $isCodeEditorNeeded = $fields->hasAnyTypeOf(CodeEditorFieldItem::class);

        seo()->title($selectedBag['title']);

        $this->pageTitle ??= __('Ayarlar');
        $this->pageDescription ??= __('Sitenizin tüm ayarlarını buradan yönetin.');

        return view('weblebby::user.preferences.show', [
            ...compact('bags', 'fields', 'isCodeEditorNeeded'),
            'selectedBagId' => $bag,
            'namespace' => $this->namespace,
            'route' => $this->route,
            'routeParams' => $this->routeParams,
            'pageTitle' => $this->pageTitle,
            'pageDescription' => $this->pageDescription,
        ]);
    }

    public function update(
        UpdatePreferenceRequest $request,
        FieldInputService $fieldInputService,
        string $namespace,
        string $bag
    ): RedirectResponse {
        $fields = Preference::fields($namespace, $bag);

        $validatedFieldValues = $fieldInputService->getFieldValues($fields, $validated = $request->validated());
        $validatedFieldValues = $fieldInputService->getDottedFieldValues($validatedFieldValues);

        preference($validatedFieldValues, locale: $validated['_locale'] ?? null, options: [
            'deleted_fields' => $validated['_deleted_fields'] ?? [],
            'reordered_fields' => $validated['_reordered_fields'] ?? [],
        ]);

        return back()->with('message', __('Ayarlar kaydedildi'));
    }
}
