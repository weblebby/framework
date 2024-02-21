<?php

namespace Weblebby\Framework\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
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
    use Cachable, HasFactory, InteractsWithMedia, Translatable;

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
        $this->addMediaConversion('lg')->fit(Manipulations::FIT_MAX, 1920, 1080);
        $this->addMediaConversion('md')->fit(Manipulations::FIT_MAX, 720, 720);
        $this->addMediaConversion('sm')->fit(Manipulations::FIT_MAX, 360, 360);
    }

    public function metafieldable(): MorphTo
    {
        return $this->morphTo();
    }

    public function toValue(
        ?FieldInterface $field,
        mixed $default = null,
        ?string $locale = null,
        array $options = []
    ): mixed {
        /** @var MetafieldService $metafieldService */
        $metafieldService = app(MetafieldService::class);

        return $metafieldService->toValue($field, $default, $locale, metafield: $this, options: $options);
    }
}
