<?php

namespace Feadmin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxonomy extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_id',
        'taxonomy',
        'description',
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

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->whereHas('term', fn (Builder $query) => $query->where('title', 'like', "%{$term}%"));
    }

    public function scopeTaxonomy(Builder $query, string $taxonomy): Builder
    {
        return $query->where('taxonomy', $taxonomy);
    }

    public function scopeTerm(Builder $query, string $term): Builder
    {
        return $query->whereHas('term', fn (Builder $query) => $query->where('slug', $term));
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
}
