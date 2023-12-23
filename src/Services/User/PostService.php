<?php

namespace Feadmin\Services\User;

use Feadmin\Contracts\Eloquent\PostInterface;
use Feadmin\Facades\Theme;
use Feadmin\Models\Taxonomy;
use Feadmin\Services\TaxonomyService;
use Illuminate\Support\Collection;

class PostService
{
    public function templates(PostInterface $postable): Collection
    {
        return Theme::active()->templatesFor($postable::class);
    }

    public function sections(PostInterface $postable, string $template = null): array
    {
        return $postable::getPostSections()
            ->withTemplateSections($postable, $template)
            ->toArray();
    }

    public function taxonomies(PostInterface $postable, string $taxonomy): ?Collection
    {
        $taxonomy = $postable::getTaxonomyFor($taxonomy);

        if (is_null($taxonomy)) {
            return null;
        }

        return Taxonomy::query()
            ->taxonomy($taxonomy->name())
            ->with('term')
            ->onlyParents()
            ->withRecursiveChildren()
            ->get();
    }

    public function syncTaxonomies(
        PostInterface $postable,
        array         $taxonomies,
        int           $primaryTaxonomyId = null
    ): array
    {
        /** @var TaxonomyService $taxonomyService */
        $taxonomyService = app(TaxonomyService::class);

        $terms = collect();

        foreach ($taxonomies as $taxonomy) {
            $terms[] = $taxonomyService->createMissingTaxonomies($taxonomy['taxonomy'], $taxonomy['terms']);
        }

        $termIds = $terms
            ->flatten()
            ->pluck('id')
            ->mapWithKeys(function ($id) use ($primaryTaxonomyId) {
                return [$id => ['is_primary' => $id === $primaryTaxonomyId]];
            });

        return $postable->taxonomies()->sync($termIds);
    }
}