<?php

namespace Feadmin\Eloquent\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Locale;

trait Translatable
{
    use \Astrotomic\Translatable\Translatable;

    public function scopeWithTranslation(Builder $query)
    {
        $query->with([
            'translations' => function (Relation $query) {
                $column = $this->getTranslationsTable() . '.' . $this->getLocaleKey();

                if ($this->useFallback()) {
                    return $query->whereIn($column, $this->getLocalesHelper()->all());
                }

                return $query->where($column, $this->locale());
            },
        ]);
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if (is_string($field) && $this->isTranslationAttribute($field)) {
            $locales = [
                $locale = app()->getLocale(),
                $this->getFallbackLocale($locale),
                ...array_filter(
                    $this->getLocalesHelper()->all(),
                    fn ($value) =>
                    str_starts_with($value, Locale::getPrimaryLanguage($locale))
                        && $value !== $locale,
                )
            ];

            foreach ($locales as $locale) {
                if ($finded = $this->whereTranslation($field, $value, $locale)->first()) {
                    return $finded;
                }
            }

            abort(404);
        }

        return parent::resolveRouteBinding($value, $field);
    }

    public function getTranslation(?string $locale = null, bool $withFallback = null): ?Model
    {
        $configFallbackLocale = $this->getFallbackLocale();
        $locale = $locale ?: $this->locale();
        $withFallback = $withFallback === null ? $this->useFallback() : $withFallback;
        $fallbackLocale = $this->getFallbackLocale($locale);

        if ($translation = $this->getTranslationByLocaleKey($locale)) {
            return $translation;
        }

        if ($withFallback && $fallbackLocale) {
            if ($translation = $this->getTranslationByLocaleKey($fallbackLocale)) {
                return $translation;
            }

            if (
                is_string($configFallbackLocale)
                && $fallbackLocale !== $configFallbackLocale
                && $translation = $this->getTranslationByLocaleKey($configFallbackLocale)
            ) {
                return $translation;
            }
        }

        if ($withFallback && $configFallbackLocale === null) {
            $configuredLocales = collect($this->getLocalesHelper()->all())
                ->sortByDesc(fn ($value) => str_starts_with($value, $locale))
                ->toArray();

            foreach ($configuredLocales as $configuredLocale) {
                if (
                    $locale !== $configuredLocale
                    && $fallbackLocale !== $configuredLocale
                    && $translation = $this->getTranslationByLocaleKey($configuredLocale)
                ) {
                    return $translation;
                }
            }
        }

        return null;
    }
}
