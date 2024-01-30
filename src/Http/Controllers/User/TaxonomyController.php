<?php

namespace Weblebby\Framework\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Facades\PostModels;
use Weblebby\Framework\Http\Requests\User\StoreTaxonomyRequest;
use Weblebby\Framework\Items\TaxonomyItem;
use Weblebby\Framework\Models\Taxonomy;
use Weblebby\Framework\Services\FieldInputService;
use Weblebby\Framework\Services\TaxonomyService;
use Weblebby\Framework\Services\User\UserTaxonomyService;

class TaxonomyController extends Controller
{
    public function index(Request $request, UserTaxonomyService $userTaxonomyService): View
    {
        $locale = $request->input('_locale', app()->getLocale());

        $taxonomyItem = $this->taxonomy($request->taxonomy, 'read');
        $taxonomies = $userTaxonomyService->getPaginatedTaxonomies($taxonomyItem, $locale);
        $taxonomiesForParentSelect = $userTaxonomyService->getTaxonomiesForParentSelect($taxonomyItem, locale: $locale);
        $fieldSections = $taxonomyItem->fieldSections()?->toArray() ?? [];
        $isTranslatable = Extension::has('multilingual');
        $taxonomy = null;

        seo()->title($taxonomyItem->pluralName());

        return view('weblebby::user.taxonomies.index', compact(
            'locale',
            'taxonomy',
            'taxonomies',
            'taxonomiesForParentSelect',
            'taxonomyItem',
            'fieldSections',
            'isTranslatable',
        ));
    }

    /**
     * @throws AuthorizationException
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function store(
        StoreTaxonomyRequest $request,
        TaxonomyService $taxonomyService,
        FieldInputService $fieldInputService,
    ): RedirectResponse {
        $taxonomyItem = $this->taxonomy($request->string('taxonomy'), 'create');
        $validated = $request->validated();

        $taxonomy = $taxonomyService->getOrCreateTaxonomy(
            taxonomy: $taxonomyItem->name(),
            term: $validated['title'],
            data: [
                'parent_id' => $validated['parent_id'],
                'slug' => $validated['slug'] ?? null,
            ],
            locale: $validated['_locale']
        );

        $fields = $taxonomyItem->fieldSections()->allFields();
        $validatedFieldValues = $fieldInputService->getFieldValues($fields, $validated);
        $validatedFieldValues = $fieldInputService->getDottedFieldValues($validatedFieldValues);

        $taxonomy->setMetafieldWithSchema($validatedFieldValues, locale: $validated['_locale']);

        return to_panel_route('taxonomies.index', ['taxonomy' => $taxonomyItem->name()])
            ->with('message', __(':taxonomy oluşturuldu', ['taxonomy' => $taxonomyItem->singularName()]));
    }

    public function edit(Request $request, Taxonomy $taxonomy, UserTaxonomyService $userTaxonomyService): View
    {
        $locale = $request->input('_locale', app()->getLocale());

        $taxonomyItem = $this->taxonomy($taxonomy->item, 'update');
        $taxonomies = $userTaxonomyService->getPaginatedTaxonomies($taxonomyItem, $locale);
        $taxonomiesForParentSelect = $userTaxonomyService->getTaxonomiesForParentSelect($taxonomyItem, $taxonomy, $locale);
        $fieldSections = $taxonomyItem->fieldSections()?->toArray() ?? [];
        $metafields = $taxonomy->getMetafieldValues(locale: $locale);
        $isTranslatable = Extension::has('multilingual');

        $taxonomy->term->setDefaultLocale($locale);

        seo()->title(__(':taxonomy düzenle', ['taxonomy' => $taxonomyItem->singularName()]));

        return view('weblebby::user.taxonomies.index', compact(
            'locale',
            'taxonomy',
            'taxonomies',
            'taxonomiesForParentSelect',
            'taxonomyItem',
            'fieldSections',
            'metafields',
            'isTranslatable',
        ));
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(
        StoreTaxonomyRequest $request,
        Taxonomy $taxonomy,
        TaxonomyService $taxonomyService,
        FieldInputService $fieldInputService,
    ): RedirectResponse {
        $validated = $request->validated();

        $taxonomy = $taxonomyService->getOrCreateTaxonomy(
            taxonomy: $taxonomy->taxonomy,
            term: $taxonomy->term,
            data: [
                'parent_id' => $validated['parent_id'],
                'title' => $validated['title'],
                'slug' => $validated['slug'] ?? null,
            ],
            locale: $validated['_locale']
        );

        $fields = $request->taxonomyItem->fieldSections()->allFields();
        $validatedFieldValues = $fieldInputService->getFieldValues($fields, $validated);
        $validatedFieldValues = $fieldInputService->getDottedFieldValues($validatedFieldValues);

        $taxonomy->setMetafieldWithSchema($validatedFieldValues, locale: $validated['_locale']);

        return to_panel_route('taxonomies.edit', [$taxonomy, 'locale' => $validated['_locale']])
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
        if (! ($taxonomy instanceof TaxonomyItem)) {
            $taxonomyItem = PostModels::taxonomy($taxonomy);
        } else {
            $taxonomyItem = $taxonomy;
        }

        abort_if(is_null($taxonomyItem), 404);
        $this->authorize($taxonomyItem->abilityFor($ability));

        return $taxonomyItem;
    }
}
