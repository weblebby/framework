<?php

namespace Feadmin\Http\Controllers\User;

use Feadmin\Facades\Localization;
use App\Http\Controllers\Controller;
use Feadmin\Support\Paginator;
use Feadmin\Http\Requests\User\StoreLocaleRequest;
use Feadmin\Models\Locale;
use Feadmin\Services\TranslationFinderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LocaleController extends Controller
{
    public function index()
    {
        $this->authorize('locale:read');

        seo()->title(__('Diller'));

        if (($defaultLocaleId = Localization::getDefaultLocaleId()) !== -1) {
            return to_panel_route('locales.show', $defaultLocaleId);
        }

        return view('feadmin::user.locales.index', [
            'availableLocales' => Localization::getAvailableLocales(),
            'remainingLocales' => Localization::getRemainingLocales(),
        ]);
    }

    public function show(Locale $locale): View|RedirectResponse
    {
        $this->authorize('locale:read');

        seo()->title(Localization::display($locale->code));

        $translations = Paginator::fromArray(Localization::getTranslations(), 50);

        return view('feadmin::user.locales.index', [
            'availableLocales' => Localization::getAvailableLocales(),
            'remainingLocales' => Localization::getRemainingLocales(),
            'translations' => $translations,
            'selectedLocale' => $locale,
        ]);
    }

    public function store(
        StoreLocaleRequest $request,
        TranslationFinderService $translationFinderService
    ): RedirectResponse {
        $validated = $request->validated();

        if (Locale::count() === 0) {
            $validated['is_default'] = true;
            $firstLocale = true;
        }

        $locale = Locale::create($validated);

        if ($firstLocale ?? false) {
            Localization::load();
            $translationFinderService->syncLocale($locale->code);
        }

        if ($request->has('is_default')) {
            DB::table('locales')
                ->where('id', Localization::getDefaultLocaleId())
                ->update(['is_default' => false]);
        }

        return to_panel_route('locales.show', $locale)
            ->with('message', __('Dil başarıyla eklendi'));
    }

    public function destroy(Locale $locale): RedirectResponse
    {
        $this->authorize('locale:delete');

        $locale->delete();

        if ($locale->is_default) {
            DB::table('locales')
                ->where('id', DB::table('locales')->min('id'))
                ->update(['is_default' => true]);
        }

        return to_panel_route('locales.index')
            ->with('message', __('Dil başarıyla silindi'));
    }

    public function sync(TranslationFinderService $service): RedirectResponse
    {
        $this->authorize('locale:translate');

        $service->syncAllLocales();

        return back()
            ->with('message', __('Çeviriler senkronize edildi'));
    }
}
