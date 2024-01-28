<?php

namespace Feadmin\Services;

use Feadmin\Enums\FieldTypeEnum;
use Feadmin\Facades\Extension;
use Feadmin\Items\Field\Contracts\FieldInterface;
use Feadmin\Items\Field\Contracts\UploadableFieldInterface;
use Feadmin\Items\Field\TextFieldItem;
// Dont forget to check the extension is installed before using it
use Feadmin\Models\Metafield;
use Weblebby\Extensions\Multilingual\Facades\Localization;

class MetafieldService
{
    public function toValue(
        FieldInterface $field,
        mixed $default = null,
        ?string $locale = null,
        ?Metafield $metafield = null,
    ): mixed {
        if ($field instanceof UploadableFieldInterface) {
            return $this->mediaToValue($field, $default, $locale, $metafield);
        }

        if ($field instanceof TextFieldItem) {
            return $this->textToValue($field, $default, $locale, $metafield);
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
    ): mixed {
        if (is_null($metafield)) {
            return $default;
        }

        if (! (Extension::has('multilingual') && $field['translatable'])) {
            return $metafield->getFirstMediaUrl();
        }

        $locales = Localization::getSupportedLocales()
            ->pluck('code')
            ->prepend(config('translatable.fallback_locale'))
            ->prepend($locale)
            ->unique()
            ->filter()
            ->values();

        foreach ($locales as $localeAsCollection) {
            if ($metafield->hasMedia($localeAsCollection)) {
                return $metafield->getFirstMediaUrl($localeAsCollection);
            }
        }
    }
}
