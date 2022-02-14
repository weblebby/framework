<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\StoreNavigationItemRequest;
use Feadmin\Models\Navigation;
use Feadmin\Models\NavigationItem;
use Core\Enums\NavigationTypeEnum;
use Illuminate\Http\RedirectResponse;

class NavigationItemController extends Controller
{
    public function store(StoreNavigationItemRequest $request, Navigation $navigation): RedirectResponse
    {
        $item = new NavigationItem($request->validated());
        $item->navigation()->associate($navigation);

        $item->position = $navigation->items()->max('position') + 1;
        $item->type = $request->type;

        if ($item->type === NavigationTypeEnum::LINKABLE) {
            $item->linkable()->associate($request->linkable_type::findOrFail($request->linkable_id));
        }

        if ($request->parent_id) {
            $item->parent()->associate($request->parent_id);
        }

        $item->save();

        return back()->with('success', t(':item başarıyla oluşturuldu.', 'admin', ['item' => $item->title]));
    }

    public function update(
        StoreNavigationItemRequest $request,
        Navigation $navigation,
        NavigationItem $item
    ): RedirectResponse {
        if ($navigation->id !== $item->navigation_id) {
            abort(404);
        }

        $item->fill($request->validated());
        $item->type = $request->type;

        if ($item->type === NavigationTypeEnum::LINKABLE) {
            $item->linkable()->associate($request->linkable_type::findOrFail($request->linkable_id));
        }

        $item->save();

        return back()->with('success', t(':item başarıyla güncellendi', 'admin', ['item' => $item->title]));
    }

    public function destroy(Navigation $navigation, NavigationItem $item): RedirectResponse
    {
        $this->authorize('navigation:update');

        if ($navigation->id !== $item->navigation_id) {
            abort(404);
        }

        $item->delete();

        return back()->with('message', t(
            ':item başarıyla silindi',
            'admin',
            ['item' => $item->title]
        ));
    }
}
