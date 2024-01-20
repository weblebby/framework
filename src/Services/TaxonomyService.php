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

    public function getOrCreateTerm(Term|string|int $term, array $data = [], ?string $locale = null): Term
    {
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        if (is_numeric($term) && ($foundTerm = $this->getTermById($term))) {
            return $foundTerm;
        }

        if ($term instanceof Term) {
            $translatedTerm = $term->translate($locale);

            if (
                is_null($translatedTerm) ||
                (filled($data['title'] ?? null) && $translatedTerm->title !== ($data['title'] ?? null)) ||
                (filled($data['slug'] ?? null) && $translatedTerm->slug !== ($data['slug'] ?? null))
            ) {
                $term->update([
                    $locale => [
                        'title' => $data['title'],
                        'slug' => $data['slug'] ?? null,
                    ],
                ]);
            }

            return $term;
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
        string $taxonomy,
        Term|string|int $term,
        array $data = [],
        ?string $locale = null,
    ): Taxonomy {
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        $term = $this->getOrCreateTerm($term, $data, $locale);

        /** @var Taxonomy $taxonomy */
        $taxonomy = Taxonomy::query()->firstOrCreate([
            'term_id' => $term->getKey(),
            'taxonomy' => $taxonomy,
        ]);

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
        array $termsOrTaxonomyIds,
        ?string $locale = null,
    ): array {
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
