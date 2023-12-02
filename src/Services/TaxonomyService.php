<?php

namespace Feadmin\Services;

use Feadmin\Models\Taxonomy;
use Feadmin\Models\Term;

class TaxonomyService
{
    public function getTermById(int $id): ?Term
    {
        /** @var Term|null $term */
        $term = Term::query()->find($id);

        return $term;
    }

    public function getOrCreateTerm(string|int $term): Term
    {
        if (is_numeric($term)) {
            if ($foundTerm = $this->getTermById($term)) {
                return $foundTerm;
            }
        }

        /** @var Term $term */
        $term = Term::query()->firstOrCreate(['title' => $term]);

        return $term;
    }

    public function getTaxonomyById(int $id): ?Taxonomy
    {
        /** @var Taxonomy|null $taxonomy */
        $taxonomy = Taxonomy::query()->find($id);

        return $taxonomy;
    }

    public function getOrCreateTaxonomy(string $taxonomy, Term|string|int $term): Taxonomy
    {
        if (is_string($term) || is_numeric($term)) {
            $term = $this->getOrCreateTerm($term);
        }

        /** @var Taxonomy $taxonomy */
        $taxonomy = Taxonomy::query()->firstOrCreate([
            'term_id' => $term->getKey(),
            'taxonomy' => $taxonomy,
        ]);

        return $taxonomy;
    }

    /**
     * @return array<int, Term>
     */
    public function createMissingTaxonomies(string $taxonomy, array $termsOrTaxonomyIds): array
    {
        $createdTerms = [];

        foreach ($termsOrTaxonomyIds as $termOrTaxonomyId) {
            $term = is_numeric($termOrTaxonomyId)
                ? $this->getTaxonomyById($termOrTaxonomyId)?->term
                : $termOrTaxonomyId;

            $createdTerms[] = $this->getOrCreateTaxonomy($taxonomy, $term);
        }

        return $createdTerms;
    }
}