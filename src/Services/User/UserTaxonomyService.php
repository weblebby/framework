<?php

namespace Weblebby\Framework\Services\User;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Weblebby\Framework\Items\TaxonomyItem;
use Weblebby\Framework\Models\Taxonomy;

class UserTaxonomyService
{
    public function getTaxonomiesBuilder(TaxonomyItem $taxonomy): Builder
    {
        return Taxonomy::query()
            ->taxonomy($taxonomy->name())
            ->with(['term' => fn ($q) => $q->withTranslation()->select('id')]);
    }

    public function getAllTaxonomies(TaxonomyItem $taxonomy): Collection
    {
        return $this->getTaxonomiesBuilder($taxonomy)->get();
    }

    public function getPaginatedTaxonomies(TaxonomyItem $taxonomy, ?string $locale = null): LengthAwarePaginator
    {
        $paginator = $this->getTaxonomiesBuilder($taxonomy)->paginate();

        if ($locale) {
            $paginator->getCollection()->transform(function (Taxonomy $taxonomy) use ($locale) {
                $taxonomy->term->setDefaultLocale($locale);

                if ($taxonomy->parent) {
                    $taxonomy->parent->term->setDefaultLocale($locale);
                }

                return $taxonomy;
            });
        }

        return $paginator;
    }

    public function getTaxonomiesForParentSelect(TaxonomyItem $taxonomy, Taxonomy|int|null $ignore = null, ?string $locale = null): Collection
    {
        if ($ignore instanceof Taxonomy) {
            $ignore = $ignore->id;
        }

        return $this->getTaxonomiesBuilder($taxonomy)
            ->when($ignore, fn (Builder $q) => $q->where('id', '!=', $ignore))
            ->get()
            ->when(
                $locale,
                fn (Collection $taxonomies) => $taxonomies->transform(function (Taxonomy $taxonomy) use ($locale) {
                    $taxonomy->term->setDefaultLocale($locale);

                    return $taxonomy;
                })
            );
    }
}
