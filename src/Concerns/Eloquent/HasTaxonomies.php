<?php

namespace Weblebby\Framework\Concerns\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Weblebby\Framework\Models\Taxable;
use Weblebby\Framework\Models\Taxonomy;
use Weblebby\Framework\Services\TaxonomyService;

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
            ->withPivot(['position', 'is_primary'])
            ->withTimestamps();
    }

    public function primaryTaxonomy(): HasOneThrough
    {
        return $this
            ->hasOneThrough(
                Taxonomy::class,
                Taxable::class,
                'taxable_id',
                'id',
                'id',
                'taxonomy_id'
            )
            ->where('taxable_type', static::class)
            ->where('is_primary', true);
    }

    public function scopeWithTaxonomies(Builder $query): Builder
    {
        return $query->with([
            'taxonomies' => fn ($q) => $q
                ->with(['term' => fn ($q) => $q->select('terms.id')->withTranslation()])
                ->select('taxonomies.id', 'taxonomies.term_id', 'taxonomies.taxonomy'),
        ]);
    }

    public function scopeHasAnyTaxonomy(Builder $query, array $taxonomyIds = []): Builder
    {
        return $query->whereHas(
            'taxonomies',
            fn (Builder $builder) => $builder->whereIn('taxonomies.id', $taxonomyIds)
        );
    }

    public function getTaxonomiesFor(string $taxonomy, ?string $locale = null): Collection
    {
        $this->loadMissing([
            'taxonomies' => fn ($q) => $q
                ->with(['term' => fn ($q) => $q->select('terms.id')->withTranslation()])
                ->select('taxonomies.id', 'taxonomies.term_id', 'taxonomies.taxonomy'),
        ]);

        return $this->taxonomies
            ->where('taxonomy', static::getTaxonomyFor($taxonomy)->name())
            ->each(fn (Taxonomy $taxonomy) => collect([$taxonomy->term])->setDefaultLocale($locale));
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
