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
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Feadmin\Facades\Extension;

// Dont forget to check the extension is installed before using it
use Weblebby\Extensions\Multilingual\Facades\Localization;

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

            return;
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

    public function toValue(FieldInterface $field, mixed $default = null, string $locale = null): mixed
    {
        if ($field instanceof UploadableFieldInterface) {
            if (Extension::has('multilingual') && $field['translatable']) {
                $locales = Localization::getSupportedLocales()
                    ->pluck('code')
                    ->prepend(config('translatable.fallback_locale'))
                    ->prepend($locale)
                    ->unique()
                    ->filter()
                    ->values();

                foreach ($locales as $locale) {
                    if ($this->hasMedia($locale)) {
                        return $this->getFirstMediaUrl($locale);
                    }
                }
            }

            return $this->getFirstMediaUrl();
        }

        if ($field instanceof TextFieldItem) {
            if (Extension::has('multilingual') && $field['translatable']) {
                return $this->translate($locale, withFallback: true)?->value;
            }

            return $this->original_value;
        }

        return $default;
    }
}
