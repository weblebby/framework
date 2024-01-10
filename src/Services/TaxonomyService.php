<?php

namespace Feadmin\Services;

use Feadmin\Models\Taxonomy;
use Feadmin\Models\Term;
use Illuminate\Support\Arr;

class TaxonomyService
{
    public function getTermById(int $id): ?Term
    {
        /** @var Term|null $term */
        $term = Term::query()->find($id);

        return $term;
    }

    public function getOrCreateTerm(string|int $term, ?string $locale = null): Term
    {
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        if (is_numeric($term)) {
            if ($foundTerm = $this->getTermById($term)) {
                return $foundTerm;
            }
        }

        /** @var Term $termModel */
        $termModel = Term::query()->whereTranslation('title', $term)->first();
        $termModel ??= Term::query()->create([$locale => ['title' => $term]]);

        return $termModel;
    }

    public function getTaxonomyById(int $id): ?Taxonomy
    {
        /** @var Taxonomy|null $taxonomy */
        $taxonomy = Taxonomy::query()->find($id);

        return $taxonomy;
    }

    public function getOrCreateTaxonomy(
        string          $taxonomy,
        Term|string|int $term,
        array           $data = [],
        ?string         $locale = null,
    ): Taxonomy
    {
        if (is_string($term) || is_numeric($term)) {
            $term = $this->getOrCreateTerm($term, $locale);
        }

        $fillable = Arr::except($data, ['parent_id']);

        /** @var Taxonomy $taxonomy */
        $taxonomy = Taxonomy::query()->updateOrCreate([
            'term_id' => $term->getKey(),
            'taxonomy' => $taxonomy,
        ], $fillable);

        if (array_key_exists('parent_id', $data)) {
            $taxonomy->parent()->associate($data['parent_id']);
            $taxonomy->save();
        }

        return $taxonomy;
    }

    /**
     * @return array<int, Term>
     */
    public function createMissingTaxonomies(
        string $taxonomy,
        array  $termsOrTaxonomyIds,
        string $locale = null,
    ): array
    {
        $createdTerms = [];

        foreach ($termsOrTaxonomyIds as $termOrTaxonomyId) {
            $term = is_numeric($termOrTaxonomyId)
                ? $this->getTaxonomyById($termOrTaxonomyId)?->term
                : $termOrTaxonomyId;

            $createdTerms[] = $this->getOrCreateTaxonomy($taxonomy, $term, locale: $locale);
        }

        return $createdTerms;
    }
}
