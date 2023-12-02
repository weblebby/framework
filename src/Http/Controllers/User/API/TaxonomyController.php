<?php

namespace Feadmin\Http\Controllers\User\API;

use App\Http\Controllers\Controller;
use Feadmin\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaxonomyController extends Controller
{
    public function index(Request $request, string $taxonomy): JsonResponse
    {
        $this->authorize('taxonomy:read');

        $taxonomies = Taxonomy::query()
            ->select('id', 'term_id')
            ->with('term:id,title')
            ->search($request->input('term'))
            ->taxonomy($taxonomy)
            ->get()
            ->map(fn (Taxonomy $taxonomy) => [
                'taxonomy_id' => $taxonomy->id,
                'title' => $taxonomy->term->title,
            ]);

        return response()->json($taxonomies);
    }
}
