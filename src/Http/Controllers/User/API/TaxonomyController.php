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
        $validated = $request->validate([
            'term' => ['required', 'string', 'max:191'],
            '_locale' => ['required', 'string'],
        ]);

        $this->authorize('taxonomy:read');

        $taxonomies = Taxonomy::query()
            ->select('id', 'term_id')
            ->with([
                'term' => fn($q) => $q->select('id')->withTranslation(),
            ])
            ->search($validated['term'], $validated['_locale'])
            ->taxonomy($taxonomy)
            ->get()
            ->map(fn(Taxonomy $taxonomy) => [
                'taxonomy_id' => $taxonomy->id,
                'title' => $taxonomy->term->title,
            ]);

        return response()->json($taxonomies);
    }
}
