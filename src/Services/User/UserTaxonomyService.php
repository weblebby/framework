<?php

namespace Feadmin\Services\User;

use Feadmin\Items\TaxonomyItem;
use Feadmin\Models\Taxonomy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserTaxonomyService
{
    public function getTaxonomiesBuilder(TaxonomyItem $taxonomy): Builder
    {
        return Taxonomy::query()
            ->taxonomy($taxonomy->name())
            ->with('term:id,title');
    }

    public function getAllTaxonomies(TaxonomyItem $taxonomy): Collection
    {
        return $this->getTaxonomiesBuilder($taxonomy)->get();
    }

    public function getPaginatedTaxonomies(TaxonomyItem $taxonomy): LengthAwarePaginator
    {
        return $this->getTaxonomiesBuilder($taxonomy)->paginate();
    }
}
