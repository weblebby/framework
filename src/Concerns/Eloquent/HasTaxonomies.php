<?php

namespace Feadmin\Concerns\Eloquent;

use Feadmin\Models\Taxonomy;
use Feadmin\Models\Term;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
            ->where('type', static::class)
            ->withPivot(['position'])
            ->withTimestamps();
    }

    public function addTaxonomy(string $term, string $taxonomy = null): void
    {
        $term = $this->getOrCreateTerm($term);
        $taxonomy = $this->getOrCreateTaxonomy($taxonomy, $term);

        $maxPosition = $this->taxonomies()
            ->where('taxonomy_id', $taxonomy->getKey())
            ->max('position');

        $this->taxonomies()->syncWithoutDetaching([
            $taxonomy->getKey() => [
                'position' => $maxPosition + 10,
            ],
        ]);
    }

    public function removeTaxonomy(string $term, string $taxonomy = null): void
    {
        $term = $this->getOrCreateTerm($term);
        $taxonomy = $this->getOrCreateTaxonomy($taxonomy, $term);

        $this->taxonomies()->detach($taxonomy->getKey());
    }

    public function hasTaxonomy(string $taxonomy, string $term): bool
    {
        $term = $this->getOrCreateTerm($term);
        $taxonomy = $this->getOrCreateTaxonomy($taxonomy, $term);

        return $this->taxonomies()->where('taxonomy_id', $taxonomy->getKey())->exists();
    }

    public function setTaxonomies(array $taxonomies): void
    {
        $this->taxonomies()->detach();

        foreach ($taxonomies as $taxonomy) {
            if (is_string($taxonomy)) {
                $taxonomy = ['term' => $taxonomy];
            }
            
            $term = $this->getOrCreateTerm($taxonomy['term']);
            $taxonomy = $this->getOrCreateTaxonomy($taxonomy['taxonomy'] ?? null, $term);

            $this->taxonomies()->syncWithoutDetaching($taxonomy->getKey());
        }
    }

    public function getOrCreateTerm(string $term): Term
    {
        /** @var Term $term */
        $term = Term::query()->firstOrCreate(['title' => $term]);

        return $term;
    }

    public function getOrCreateTaxonomy(?string $taxonomy, Term|string $term): Taxonomy
    {
        if (is_null($taxonomy)) {
            $taxonomy = static::class;
        }

        if (is_string($term)) {
            $term = $this->getOrCreateTerm($term);
        }

        /** @var Taxonomy $taxonomy */
        $taxonomy = Taxonomy::query()->firstOrCreate([
            'term_id' => $term->getKey(),
            'taxonomy' => $taxonomy,
        ]);

        return $taxonomy;
    }
}
