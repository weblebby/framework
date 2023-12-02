<?php

namespace Feadmin\Http\Controllers\User\API;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\SortNavigationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

        return response()->json(['message' => __('Menü sıralaması kaydedildi')]);
    }
}