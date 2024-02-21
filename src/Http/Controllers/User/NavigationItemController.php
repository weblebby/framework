<?php

namespace Weblebby\Framework\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Weblebby\Framework\Enums\NavigationTypeEnum;
use Weblebby\Framework\Http\Requests\User\StoreNavigationItemRequest;
use Weblebby\Framework\Models\Navigation;
use Weblebby\Framework\Models\NavigationItem;

class NavigationItemController extends Controller
{
    public function store(StoreNavigationItemRequest $request, Navigation $navigation): RedirectResponse
    {
        $item = new NavigationItem($request->safeWithCasts());
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

        return back()->with('message', __(':title created', ['title' => $item->title]));
    }

    public function update(
        StoreNavigationItemRequest $request,
        Navigation $navigation,
        NavigationItem $item
    ): RedirectResponse {
        abort_if($navigation->id !== $item->navigation_id, 404);

        $item->fill($request->safeWithCasts());
        $item->type = $request->type;

        if ($item->type === NavigationTypeEnum::LINKABLE) {
            $item->linkable()->associate($request->linkable_type::findOrFail($request->linkable_id));
        }

        $item->save();

        return back()->with('message', __(':title updated', ['title' => $item->title]));
    }

    public function destroy(Navigation $navigation, NavigationItem $item): RedirectResponse
    {
        $this->authorize('navigation:update');
        abort_if($navigation->id !== $item->navigation_id, 404);

        $item->delete();

        return back()->with('message', __(':title deleted', ['title' => $item->title]));
    }
}
