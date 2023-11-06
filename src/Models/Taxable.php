<?php

namespace Feadmin\Models;

use Feadmin\Concerns\Eloquent\HasPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Taxable extends Model
{
    use HasFactory, HasPosition;

    protected $fillable = [
        'position',
    ];

    public function maxPosition(): int
    {
        return $this->taxable->taxonomies()
            ->where('taxonomy_id', $this->taxonomy_id)
            ->max('position');
    }

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function taxable(): MorphTo
    {
        return $this->morphTo();
    }
}
