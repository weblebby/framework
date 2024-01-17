<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Extension;
use Feadmin\Http\Requests\User\StoreNavigationRequest;
use Feadmin\Http\Requests\User\UpdateNavigationRequest;
use Feadmin\Models\Navigation;
use Feadmin\Services\NavigationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NavigationController extends Controller
{
    public function index(NavigationService $navigationService): View|RedirectResponse
    {
        $this->authorize('navigation:read');

        seo()->title(__('Menüler'));

        $firstNavigation = Navigation::query()->first();

        if ($firstNavigation) {
            return to_panel_route('navigations.show', $firstNavigation);
        }

        return view('feadmin::user.navigations.index', [
            'navigations' => $navigationService->getForListing(),
        ]);
    }

    public function store(StoreNavigationRequest $request): RedirectResponse
    {
        $navigation = Navigation::query()->create($request->validated());

        return to_panel_route('navigations.show', $navigation)
            ->with('message', __(':navigation oluşturuldu.', ['navigation' => $navigation->title]));
    }

    public function show(Request $request, Navigation $navigation, NavigationService $navigationService): View
    {
        $this->authorize('navigation:read');

        $isTranslatable = Extension::has('multilingual');
        $locale = $request->string('_locale', app()->getLocale())->toString();

        $navigation->load(['items' => function ($query) {
            $query
                ->withRecursiveChildren()
                ->whereNull('parent_id')
                ->oldest('position');
        }]);

        $navigation->items->setDefaultLocale($locale, 'children');

        seo()->title($navigation->title);

        return view('feadmin::user.navigations.index', [
            'navigations' => $navigationService->getForListing(),
            'selectedNavigation' => $navigation,
            'isTranslatable' => $isTranslatable,
            'locale' => $locale,
        ]);
    }

    public function update(UpdateNavigationRequest $request, Navigation $navigation): RedirectResponse
    {
        $navigation->update($request->validated());

        return back()->with('message', __(':navigation güncellendi.', ['navigation' => $navigation->title]));
    }

    public function destroy(Navigation $navigation)
    {
        $this->authorize('navigation:delete');

        $navigation->delete();

        return to_panel_route('navigations.index')
            ->with('message', __('Menü silindi'));
    }
}
