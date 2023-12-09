<?php

namespace Feadmin\Concerns\Eloquent;

use Feadmin\Models\Taxonomy;
use Feadmin\Models\Term;
use Feadmin\Services\TaxonomyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait HasTaxonomies
{
    protected static function bootHasTaxonomies(): void
    {
        static::deleting(function (Model $model) {
            $model->taxonomies()->detach();
        });
    }

    public function taxonomies(): MorphToMany
    {
        return $this->morphToMany(Taxonomy::class, 'taxable')
            ->withPivot(['position'])
            ->withTimestamps();
    }

    public function scopeWithTaxonomies(Builder $query): Builder
    {
        return $query->with(['taxonomies:id,term_id,taxonomy' => [
            'term:id,title',
        ]]);
    }

    public function getTaxonomiesFor(string $taxonomy): Collection
    {
        return $this->taxonomies->where('taxonomy', static::getTaxonomyFor($taxonomy)->name());
    }

    public function addTaxonomy(string $taxonomy, array|string $terms): void
    {
        if (is_array($terms)) {
            foreach ($terms as $term) {
                $this->addTaxonomy($taxonomy, $term);
            }

            return;
        }

        /** @var TaxonomyService $taxonomyService */
        $taxonomyService = app(TaxonomyService::class);

        $term = $taxonomyService->getOrCreateTerm($terms);
        $taxonomy = $taxonomyService->getOrCreateTaxonomy($taxonomy, $term);

        $maxPosition = $this->taxonomies()
            ->where('taxonomy_id', $taxonomy->getKey())
            ->max('position');

        $this->taxonomies()->syncWithoutDetaching([
            $taxonomy->getKey() => [
                'position' => $maxPosition + 10,
            ],
        ]);
    }

    public function removeTaxonomy(string $taxonomy, string $term): void
    {
        /** @var TaxonomyService $taxonomyService */
        $taxonomyService = app(TaxonomyService::class);

        $term = $taxonomyService->getOrCreateTerm($term);
        $taxonomy = $taxonomyService->getOrCreateTaxonomy($taxonomy, $term);

        $this->taxonomies()->detach($taxonomy->getKey());
    }

    public function hasTaxonomy(string $taxonomy, string $term): bool
    {
        /** @var TaxonomyService $taxonomyService */
        $taxonomyService = app(TaxonomyService::class);

        $term = $taxonomyService->getOrCreateTerm($term);
        $taxonomy = $taxonomyService->getOrCreateTaxonomy($taxonomy, $term);

        return $this->taxonomies()->where('taxonomy_id', $taxonomy->getKey())->exists();
    }

    public function setTaxonomies(string $taxonomy, array|string $terms): void
    {
        /** @var TaxonomyService $taxonomyService */
        $taxonomyService = app(TaxonomyService::class);

        $this->taxonomies()->detach();

        foreach ($terms as $term) {
            $term = $taxonomyService->getOrCreateTerm($term);
            $taxonomy = $taxonomyService->getOrCreateTaxonomy($taxonomy, $term);

            $this->taxonomies()->syncWithoutDetaching($taxonomy->getKey());
        }
    }
}
