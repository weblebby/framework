<?php

namespace Feadmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Navigation extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'handle',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('handle');
    }

    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class);
    }
}
