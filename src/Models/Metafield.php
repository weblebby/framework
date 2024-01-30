<?php

namespace Weblebby\Framework\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Weblebby\Extensions\Multilingual\Facades\Localization;
use Weblebby\Framework\Concerns\Eloquent\Translatable;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Items\Field\Contracts\FieldInterface;
// Dont forget to check the extension is installed before using it
use Weblebby\Framework\Services\MetafieldService;

class Metafield extends Model implements HasMedia, TranslatableContract
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
        if (Extension::has('multilingual')) {
            $locales = Localization::getSupportedLocales()->pluck('code');

            foreach ($locales as $locale) {
                $this->addMediaCollection($locale)->singleFile();
            }
        }

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

    public function toValue(FieldInterface $field, mixed $default = null, ?string $locale = null): mixed
    {
        /** @var MetafieldService $metafieldService */
        $metafieldService = app(MetafieldService::class);

        return $metafieldService->toValue($field, $default, $locale, $this);
    }
}
