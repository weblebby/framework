<?php

namespace Feadmin\Models;

use Feadmin\Concerns\Eloquent\Translatable;
use Feadmin\Items\Field\Contracts\FieldInterface;
use Feadmin\Items\Field\Contracts\UploadableFieldInterface;
use Feadmin\Items\Field\TextFieldItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Metafield extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Translatable;

    protected $fillable = [
        'key',
        'original_value',
    ];

    public array $translatedAttributes = [
        'value',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->singleFile();
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('lg')->width(1920)->height(1080);
        $this->addMediaConversion('sm')->width(400)->height(225);
    }

    public function metafieldable(): MorphTo
    {
        return $this->morphTo();
    }

    public function toValue(FieldInterface $field, mixed $default = null): mixed
    {
        if ($field instanceof UploadableFieldInterface) {
            return $this->getFirstMediaUrl();
        }

        if ($field instanceof TextFieldItem) {
            return $this->value ?? $this->original_value;
        }

        return $default;
    }
}
