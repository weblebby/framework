<?php

namespace Feadmin\Services\User;

use Feadmin\Contracts\Eloquent\PostInterface;
use Feadmin\Services\TaxonomyService;

class PostService
{
    public function attachTaxonomies(PostInterface $postable, array $taxonomies): void
    {
        /** @var TaxonomyService $taxonomyService */
        $taxonomyService = app(TaxonomyService::class);

        $terms = collect();

        foreach ($taxonomies as $taxonomy) {
            $terms[] = $taxonomyService->createMissingTaxonomies($taxonomy['taxonomy'], $taxonomy['terms']);
        }

        $postable->taxonomies()->sync($terms->flatten()->pluck('id'));
    }
}