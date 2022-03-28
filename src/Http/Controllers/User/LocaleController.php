<?php

namespace Feadmin\Http\Controllers\User;

use Feadmin\Facades\Localization;
use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\StoreLocaleRequest;
use Feadmin\Models\Locale;
use Feadmin\Services\TranslationFinderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LocaleController extends Controller
{
    public function index()
    {
        $this->authorize('locale:read');

        seo()->title(t('Diller', 'panel'));

        if (($defaultLocaleId = Localization::getDefaultLocaleId()) !== -1) {
            return redirect()->route('admin::locales.show', $defaultLocaleId);
        }

        return view('feadmin::user.locales.index', [
            'availableLocales' => Localization::getAvailableLocales(),
            'remainingLocales' => Localization::getRemainingLocales(),
        ]);
    }

    public function show(Request $request, Locale $locale): View|RedirectResponse
    {
        $this->authorize('locale:read');

        $groups = Localization::groups();

        if (!is_string($request->group)) {
            return redirect()->route('admin::locales.show', [
                $locale->id,
                'group' => $groups->keys()->first()
            ]);
        }

        seo()->title(Localization::display($locale->code));

        $translations = Localization::getTranslations()
            ->where('group', $request->group)
            ->sortByDesc(function ($translation) {
                return $translation->locale_id === Localization::getDefaultLocaleId();
            })
            ->unique('key');

        return view('feadmin::user.locales.index', [
            'availableLocales' => Localization::getAvailableLocales(),
            'remainingLocales' => Localization::getRemainingLocales(),
            'selectedGroup' => $groups->get($request->group) ?? null,
            'selectedLocale' => $locale,
            'translations' => $translations,
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
            $translationFinderService->scan();
        }

        if ($request->has('is_default')) {
            DB::table('locales')
                ->where('id', Localization::getDefaultLocaleId())
                ->update(['is_default' => false]);
        }

        return redirect()
            ->route('admin::locales.show', $locale)
            ->with('message', t('Dil başarıyla eklendi', 'panel'));
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

        return redirect()
            ->route('admin::locales.index')
            ->with('message', t('Dil başarıyla silindi', 'panel'));
    }

    public function sync(TranslationFinderService $service): RedirectResponse
    {
        $this->authorize('locale:translate');

        $service->scan();

        return back()
            ->with('message', t('Çeviriler senkronize edildi', 'panel'));
    }
}
