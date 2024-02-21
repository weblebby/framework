<?php

namespace Weblebby\Framework\Http\Controllers\User\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Weblebby\Framework\Http\Requests\User\SortNavigationRequest;

class NavigationSortController extends Controller
{
    public function update(SortNavigationRequest $request): JsonResponse
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
                    'parent_id' => $item['parent_id'] ?? null,
                ]);

            $positions[$depth]++;
        }

        return response()->json(['message' => __('Menu order saved')]);
    }
}
