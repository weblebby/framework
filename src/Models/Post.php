<?php

namespace Feadmin\Models;

use Feadmin\Concerns\Eloquent\HasMetafields;
use Feadmin\Concerns\Eloquent\HasOwner;
use Feadmin\Enums\HasOwnerEnum;
use Feadmin\Enums\PostStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model implements HasMedia
{
    use HasFactory, HasMetafields, HasOwner, HasSlug, InteractsWithMedia;

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

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }
}
