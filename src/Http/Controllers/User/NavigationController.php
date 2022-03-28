<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\SortNavigationRequest;
use Feadmin\Http\Requests\User\StoreNavigationRequest;
use Feadmin\Http\Requests\User\UpdateNavigationRequest;
use Feadmin\Models\Navigation;
use Core\Services\NavigationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NavigationController extends Controller
{
    public function index(NavigationService $navigationService): View|RedirectResponse
    {
        $this->authorize('navigation:read');

        seo()->title(t('Menüler', 'panel'));

        $firstNavigation = Navigation::first();

        if ($firstNavigation) {
            return redirect()->route('admin::navigations.show', $firstNavigation);
        }

        return view('feadmin::user.navigations.index', [
            'navigations' => $navigationService->getForListing(),
            'smartMenuItems' => $navigationService->smartMenuItems(),
        ]);
    }

    public function store(StoreNavigationRequest $request): RedirectResponse
    {
        $navigation = Navigation::create($request->validated());

        return redirect()
            ->route('admin::navigations.show', $navigation)
            ->with('message', t('Menü başarıyla oluşturuldu.', 'panel'));
    }

    public function show(Navigation $navigation, NavigationService $navigationService): View
    {
        $this->authorize('navigation:read');

        seo()->title($navigation->title);

        $navigation->load(['items' => function ($query) {
            $query
                ->withRecursiveChildren()
                ->whereNull('parent_id')
                ->oldest('position');
        }]);

        return view('feadmin::user.navigations.index', [
            'navigations' => $navigationService->getForListing(),
            'smartMenuItems' => $navigationService->smartMenuItems(),
            'selectedNavigation' => $navigation,
        ]);
    }

    public function update(UpdateNavigationRequest $request, Navigation $navigation): RedirectResponse
    {
        $navigation->update($request->validated());

        return back()->with('message', t('Menü başarıyla güncellendi.', 'panel'));
    }

    public function destroy(Navigation $navigation)
    {
        $this->authorize('navigation:delete');

        $navigation->delete();

        return redirect()
            ->route('admin::navigations.index')
            ->with('message', t('Menü silindi', 'panel'));
    }

    public function sort(SortNavigationRequest $request): JsonResponse
    {
        $this->authorize('navigation:update');

        $positions = [];

        foreach ($request->items as $item) {
            $depth = $item['depth'];
            $positions[$depth] ??= 0;

            DB::table('navigation_items')
                ->where('id', $item['id'])
                ->update([
                    'position' => $positions[$depth],
                    'parent_id' => $item['parent_id'] ?? null
                ]);

            $positions[$depth]++;
        }

        return response()->json([
            'success' => true,
            'message' => t('Menü sıralaması kaydedildi', 'panel')
        ]);
    }
}
