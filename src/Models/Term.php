<?php

namespace Weblebby\Framework\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Weblebby\Framework\Concerns\Eloquent\Translatable;

class Term extends Model implements TranslatableContract
{
    use Cachable, HasFactory, Translatable;

    public $translatedAttributes = [
        'title',
        'slug',
    ];

    public function taxonomies(): HasMany
    {
        return $this->hasMany(Taxonomy::class);
    }
}
