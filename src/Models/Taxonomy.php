<?php

namespace Weblebby\Framework\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Weblebby\Framework\Concerns\Eloquent\HasMetafields;
use Weblebby\Framework\Facades\PostModels;
use Weblebby\Framework\Items\TaxonomyItem;

class Taxonomy extends Model
{
    use Cachable, HasFactory, HasMetafields;

    protected $fillable = [
        'term_id',
        'taxonomy',
    ];

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Taxonomy::class, 'parent_id');
    }

    public function taxables(): HasMany
    {
        return $this->hasMany(Taxable::class);
    }

    public function scopeSearch(Builder $query, ?string $term, ?string $locale = null): Builder
    {
        if (blank($term)) {
            return $query;
        }

        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        return $query->whereHas(
            'term',
            fn (Builder $query) => $query->whereTranslationLike('title', "%{$term}%", $locale)
        );
    }

    public function scopeTaxonomy(Builder $query, TaxonomyItem|string|array $taxonomy): Builder
    {
        if ($taxonomy instanceof TaxonomyItem) {
            $taxonomy = $taxonomy->name();
        }

        if (is_array($taxonomy)) {
            $taxonomies = [];

            foreach ($taxonomy as $item) {
                $taxonomies[] = $item instanceof TaxonomyItem
                    ? $item->name()
                    : $item;
            }

            return $query->whereIn('taxonomy', $taxonomies);
        }

        return $query->where('taxonomy', $taxonomy);
    }

    public function scopeTerm(Builder $query, ?string $term = null): Builder
    {
        if (is_null($term)) {
            return $query->where('id', false);
        }

        return $query->whereHas('term', fn (Builder $query) => $query->whereTranslation('slug', $term));
    }

    public function scopeParent(Builder $query, string $parent): Builder
    {
        return $query->whereHas('parent', fn (Builder $query) => $query->where('slug', $parent));
    }

    public function scopeOnlyParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWithRecursiveChildren(Builder $query): Builder
    {
        return $query->with(['children' => fn (HasMany $query) => $query->withRecursiveChildren()]);
    }

    protected function item(): Attribute
    {
        return Attribute::get(fn () => PostModels::taxonomy($this->taxonomy));
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => route('content', "{$this->item->slug()}/{$this->term->slug}"));
    }
}
