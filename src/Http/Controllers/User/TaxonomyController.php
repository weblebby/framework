<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\PostModels;
use Feadmin\Http\Requests\User\StoreTaxonomyRequest;
use Feadmin\Http\Requests\User\UpdatePostRequest;
use Feadmin\Items\TaxonomyItem;
use Feadmin\Models\Post;
use Feadmin\Models\Taxonomy;
use Feadmin\Services\TaxonomyService;
use Feadmin\Services\User\UserTaxonomyService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    public function index(Request $request, UserTaxonomyService $userTaxonomyService): View
    {
        $taxonomyItem = $this->taxonomy($request->taxonomy, 'read');
        $taxonomies = $userTaxonomyService->getPaginatedTaxonomies($taxonomyItem);
        $taxonomiesForParentSelect = $userTaxonomyService->getTaxonomiesBuilder($taxonomyItem)->get();
        $taxonomy = null;

        seo()->title($taxonomyItem->pluralName());

        return view('feadmin::user.taxonomies.index', compact(
            'taxonomy',
            'taxonomies',
            'taxonomiesForParentSelect',
            'taxonomyItem'
        ));
    }

    public function store(StoreTaxonomyRequest $request, TaxonomyService $taxonomyService): RedirectResponse
    {
        $taxonomyItem = $this->taxonomy($request->taxonomy, 'create');

        $taxonomyService->getOrCreateTaxonomy($taxonomyItem->name(), $request->title, [
            'parent_id' => $request->parent_id,
            'description' => $request->description,
        ]);

        return to_panel_route('taxonomies.index', ['taxonomy' => $taxonomyItem->name()])
            ->with('message', __(':taxonomy oluşturuldu', ['taxonomy' => $taxonomyItem->singularName()]));
    }

    public function edit(Taxonomy $taxonomy, UserTaxonomyService $userTaxonomyService): View
    {
        $taxonomyItem = $this->taxonomy($taxonomy->item, 'update');

        $taxonomies = $userTaxonomyService->getPaginatedTaxonomies($taxonomyItem);

        $taxonomiesForParentSelect = $userTaxonomyService->getTaxonomiesBuilder($taxonomyItem)
            ->whereNot('id', $taxonomy->id)
            ->get();

        seo()->title(__(':taxonomy düzenle', ['taxonomy' => $taxonomyItem->singularName()]));

        return view('feadmin::user.taxonomies.index', compact(
            'taxonomy',
            'taxonomies',
            'taxonomiesForParentSelect',
            'taxonomyItem',
        ));
    }

    public function update(StoreTaxonomyRequest $request, Taxonomy $taxonomy, TaxonomyService $taxonomyService): RedirectResponse
    {
        $taxonomyService->getOrCreateTaxonomy($taxonomy->taxonomy, $taxonomy->term, [
            'parent_id' => $request->parent_id,
            'description' => $request->description,
        ]);

        return to_panel_route('taxonomies.edit', $taxonomy)
            ->with('message', __(':taxonomy güncellendi', ['taxonomy' => $taxonomy->item->singularName()]));
    }

    public function destroy(Taxonomy $taxonomy): RedirectResponse
    {
        $this->taxonomy($taxonomy->item, 'delete');

        $taxonomy->delete();

        return to_panel_route('taxonomies.index', ['taxonomy' => $taxonomy->item->name()])
            ->with('message', __(':taxonomy silindi', ['taxonomy' => $taxonomy->item->singularName()]));
    }

    /**
     * @throws AuthorizationException
     */
    protected function taxonomy(TaxonomyItem|string $taxonomy, string $ability): TaxonomyItem
    {
        if (!($taxonomy instanceof TaxonomyItem)) {
            $taxonomyItem = PostModels::taxonomy($taxonomy);
        } else {
            $taxonomyItem = $taxonomy;
        }

        abort_if(is_null($taxonomyItem), 404);
        $this->authorize($taxonomyItem->abilityFor($ability));

        return $taxonomyItem;
    }
}
