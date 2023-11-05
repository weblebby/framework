<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\StoreNavigationItemRequest;
use Feadmin\Models\Navigation;
use Feadmin\Models\NavigationItem;
use Feadmin\Enums\NavigationTypeEnum;
use Illuminate\Http\RedirectResponse;

class NavigationItemController extends Controller
{
    public function store(StoreNavigationItemRequest $request, Navigation $navigation): RedirectResponse
    {
        $this->authorize('navigation:create');

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

        return back()->with('success', __(':item başarıyla oluşturuldu.', ['item' => $item->title]));
    }

    public function update(
        StoreNavigationItemRequest $request,
        NavigationItem             $item
    ): RedirectResponse
    {
        $this->authorize('navigation:update');

        $item->fill($request->validated());
        $item->type = $request->type;

        if ($item->type === NavigationTypeEnum::LINKABLE) {
            $item->linkable()->associate($request->linkable_type::findOrFail($request->linkable_id));
        }

        $item->save();

        return back()->with('success', __(':item başarıyla güncellendi', ['item' => $item->title]));
    }

    public function destroy(NavigationItem $item): RedirectResponse
    {
        $this->authorize('navigation:delete');

        $item->delete();

        return back()->with('message', __(':item başarıyla silindi', ['item' => $item->title]));
    }
}
