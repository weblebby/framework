<?php

namespace Weblebby\Framework\Services\User;

use Illuminate\Support\Collection;
use Weblebby\Framework\Contracts\Eloquent\PostInterface;
use Weblebby\Framework\Facades\Theme;
use Weblebby\Framework\Items\FieldSectionsItem;
use Weblebby\Framework\Models\Taxonomy;
use Weblebby\Framework\Services\TaxonomyService;

class PostService
{
    public function templates(PostInterface $postable): Collection
    {
        return Theme::active()?->templatesFor($postable::class) ?? collect();
    }

    public function sections(PostInterface $postable, ?string $template = null): FieldSectionsItem
    {
        return $postable::getPostSections()->withTemplateSections($postable, $template);
    }

    public function taxonomies(PostInterface $postable, string $taxonomy, ?string $locale = null): ?Collection
    {
        $taxonomy = $postable::getTaxonomyFor($taxonomy);

        if (is_null($taxonomy)) {
            return null;
        }

        return Taxonomy::query()
            ->taxonomy($taxonomy->name())
            ->with(['term' => fn ($query) => $query->select('id')->withTranslation()])
            ->onlyParents()
            ->withRecursiveChildren()
            ->get()
            ->map(function (Taxonomy $taxonomy) use ($locale) {
                $taxonomy->term->setDefaultLocale($locale);

                return $taxonomy;
            });
    }

    public function syncTaxonomies(
        PostInterface $postable,
        array $taxonomies,
        ?int $primaryTaxonomyId = null,
        ?string $locale = null,
    ): array {
        /** @var TaxonomyService $taxonomyService */
        $taxonomyService = app(TaxonomyService::class);

        $terms = collect();

        foreach ($taxonomies as $taxonomy) {
            $terms[] = $taxonomyService->createMissingTaxonomies(
                $taxonomy['taxonomy'],
                $taxonomy['terms'],
                locale: $locale,
            );
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
