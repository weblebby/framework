<?php

namespace Weblebby\Framework\Services;

use Illuminate\Support\Collection;
use Weblebby\Extensions\Multilingual\Facades\Localization;
use Weblebby\Framework\Enums\FieldTypeEnum;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Items\Field\Contracts\FieldInterface;
use Weblebby\Framework\Items\Field\Contracts\UploadableFieldInterface;
// Dont forget to check the extension is installed before using it
use Weblebby\Framework\Items\Field\TextFieldItem;
use Weblebby\Framework\Models\Metafield;

class MetafieldService
{
    public function toValue(
        ?FieldInterface $field,
        mixed $default = null,
        ?string $locale = null,
        ?Metafield $metafield = null,
        array $options = []
    ): mixed {
        if ($field instanceof UploadableFieldInterface) {
            return $this->mediaToValue($field, $default, $locale, $metafield, $options);
        }

        if ($field instanceof TextFieldItem) {
            return $this->textToValue($field, $default, $locale, $metafield);
        }

        if (filled($metafield?->original_value)) {
            return $this->metafieldToValue($metafield, $default);
        }

        return $default;
    }

    public function textToValue(
        FieldInterface $field,
        mixed $default = null,
        ?string $locale = null,
        ?Metafield $metafield = null,
    ): mixed {
        $isTranslatable = Extension::has('multilingual') && $field['translatable'];

        if ($metafield) {
            $value = $isTranslatable
                ? $metafield->translate($locale, withFallback: true)?->value
                : $metafield->original_value;
        }

        $value ??= $field['default'] ?? $default;

        if ($field['type'] === FieldTypeEnum::TEL) {
            return phone($value);
        }

        return $value;
    }

    public function mediaToValue(
        FieldInterface $field,
        mixed $default = null,
        ?string $locale = null,
        ?Metafield $metafield = null,
        array $options = [],
    ): mixed {
        if (is_null($metafield)) {
            return $default;
        }

        $mediaAsUrl = $options['media_as_url'] ?? false;

        if (! (Extension::has('multilingual') && $field['translatable'])) {
            return $mediaAsUrl ? $metafield->getFirstMediaUrl() : $metafield->getFirstMedia();
        }

        foreach ($this->getLocalizableMediaCollections($locale) as $localeAsCollection) {
            if ($metafield->hasMedia($localeAsCollection)) {
                return $mediaAsUrl
                    ? $metafield->getFirstMediaUrl($localeAsCollection)
                    : $metafield->getFirstMedia($localeAsCollection);
            }
        }

        return null;
    }

    public function metafieldToValue(Metafield $metafield, mixed $default = null, array $options = []): mixed
    {
        if ($metafield->original_value === '1' && $metafield->hasMedia()) {
            $mediaAsUrl = $options['media_as_url'] ?? false;

            return $mediaAsUrl
                ? $metafield->getFirstMediaUrl()
                : $metafield->getFirstMedia();
        }

        return $metafield->original_value ?? $default;
    }

    private function getLocalizableMediaCollections(?string $locale = null): Collection
    {
        if (! Extension::has('multilingual')) {
            return collect();
        }

        return Localization::getSupportedLocales()
            ->pluck('code')
            ->prepend(config('translatable.fallback_locale'))
            ->prepend($locale)
            ->unique()
            ->filter()
            ->values();
    }
}
