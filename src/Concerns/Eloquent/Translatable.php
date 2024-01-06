<?php

namespace Feadmin\Concerns\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

trait Translatable
{
    use \Astrotomic\Translatable\Translatable;

    public function scopeWithTranslation(Builder $query): void
    {
        $query->with([
            'translations' => function (Relation $query) {
                $column = $this->getTranslationsTable().'.'.$this->getLocaleKey();

                if ($this->useFallback()) {
                    return $query->whereIn($column, $this->getLocalesHelper()->all());
                }

                return $query->where($column, $this->locale());
            },
        ]);
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $model = parent::resolveRouteBinding($value, $field);

        if ($model && is_string($field) && $this->isTranslationAttribute($field)) {
            $firstBindingKey = head(array_keys(request()->route()->bindingFields()));

            if ($route = $this->translatedRoute($model, $value, $field, $firstBindingKey)) {
                abort(redirect()->to($route));
            }
        }

        return $model;
    }

    public function resolveChildRouteBinding($childType, $value, $field): ?Model
    {
        $model = parent::resolveChildRouteBinding($childType, $value, $field);

        if ($model && is_string($field) && $this->isTranslationAttribute($field)) {
            if ($route = $this->translatedRoute($model, $value, $field, $childType)) {
                abort(redirect()->to($route));
            }
        }

        return $model;
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        if (is_string($field) && $this->isTranslationAttribute($field)) {
            return $query->whereTranslation($field, $value);
        }

        return parent::resolveRouteBindingQuery($query, $value, $field);
    }

    public function getTranslation(?string $locale = null, ?bool $withFallback = null): ?Model
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

    public function translatedRoute($model, string $value, string $field, string $key): ?string
    {
        if ($value === $model->$field) {
            return null;
        }

        return route(request()->route()->getName(), [
            ...request()->route()->parameters(),
            ...request()->query(),
            $key => $model->$field,
        ]);
    }
}
