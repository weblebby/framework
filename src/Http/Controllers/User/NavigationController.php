<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\SortNavigationRequest;
use Feadmin\Http\Requests\User\StoreNavigationRequest;
use Feadmin\Http\Requests\User\UpdateNavigationRequest;
use Feadmin\Models\Navigation;
use Feadmin\Services\NavigationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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
            'smartMenuItems' => $navigationService->smartMenuItems(),
        ]);
    }

    public function store(StoreNavigationRequest $request): RedirectResponse
    {
        $navigation = Navigation::query()->create($request->validated());

        return to_panel_route('navigations.show', $navigation)
            ->with('message', __('Menü başarıyla oluşturuldu.'));
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

        return back()->with('message', __('Menü başarıyla güncellendi.'));
    }

    public function destroy(Navigation $navigation)
    {
        $this->authorize('navigation:delete');

        $navigation->delete();

        return to_panel_route('navigations.index')
            ->with('message', __('Menü silindi'));
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
            'message' => __('Menü sıralaması kaydedildi')
        ]);
    }
}
