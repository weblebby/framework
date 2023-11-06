<?php

namespace Feadmin\Models;

use Feadmin\Concerns\Eloquent\HasMetafields;
use Feadmin\Concerns\Eloquent\HasOwner;
use Feadmin\Concerns\Eloquent\HasPosition;
use Feadmin\Concerns\Eloquent\HasTaxonomies;
use Feadmin\Enums\HasOwnerEnum;
use Feadmin\Enums\PostStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model implements HasMedia
{
    use HasFactory,
        HasMetafields,
        HasTaxonomies,
        HasOwner,
        HasSlug,
        HasPosition,
        InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'type',
        'position',
    ];

    protected $casts = [
        'status' => PostStatusEnum::class,
        'published_at' => 'datetime',
    ];

    public array $userTouches = [
        HasOwnerEnum::CREATED_BY,
        HasOwnerEnum::UPDATED_BY,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', static::class);
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->doNotGenerateSlugsOnUpdate()
            ->saveSlugsTo('slug');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    public function scopeTyped(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
