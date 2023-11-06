<?php

namespace Feadmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxonomy extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function taxables(): HasMany
    {
        return $this->hasMany(Taxable::class);
    }
}
