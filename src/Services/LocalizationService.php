<?php

namespace Feadmin\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use IntlDateFormatter;
use Locale;
use ResourceBundle;

class LocalizationService
{
    private Collection $availableLocales;

    private Collection $allLocales;

    private object $defaultLocale;

    private object $currentLocale;

    private object $translations;

    private Collection $groups;

    public function __construct()
    {
        $this->load();
    }

    public function load(): void
    {
        $this->groups = collect();

        $this->allLocales = collect(config('feadmin.all_locales'))
            ->map(function ($locale, $code) {
                $locale['code'] = $code;

                return $locale;
            });

        $this->availableLocales = DB::table('locales')->get();
        $this->translations = DB::table('locale_translations')->get();

        $this->setDefaultLocale();
        $this->setCurrentLocale(app()->getLocale());
    }

    public function get(
        string $key,
        string $group = 'default',
        array $replace = [],
        string $code = null
    ): string|Collection {
        $locale = $code ? $this->getLocale($code) : $this->getCurrentLocale();

        $translation = $this->translations
            ->where('group', $group)
            ->where('key', $key)
            ->where('locale_id', $locale->id)
            ->first();

        if (is_null($translation) && $locale->code !== 'tr') {
            $translation = $this->translations
                ->where('group', $group)
                ->where('key', $key)
                ->where('locale_id', $this->getDefaultLocaleId())
                ->first();
        }

        $translated = $translation->value ?? $key;

        foreach ($replace as $key => $value) {
            $translated = str_replace(":{$key}", e($value), $translated);
        }

        return $translated;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getTranslationsForClient(): Collection
    {
        return $this->getTranslations()
            ->where('locale_id', $this->getCurrentLocaleId())
            ->map(fn ($translation) => [
                'group' => $translation->group,
                'key' => $translation->key,
                'value' => $translation->value,
            ])
            ->values();
    }

    public function getDefaultLocale(): object
    {
        return $this->defaultLocale;
    }

    public function getDefaultLocaleId(): int
    {
        return $this->getDefaultLocale()->id;
    }

    public function getCurrentLocale(): object
    {
        return $this->currentLocale;
    }

    public function getCurrentLocaleId(): int
    {
        return $this->getCurrentLocale()->id;
    }

    public function getLocale(string $code): object
    {
        return $this->getAvailableLocales()->firstWhere('code', $code);
    }

    public function getAvailableLocales(): Collection
    {
        return $this->availableLocales;
    }

    public function getAllLocales(): Collection
    {
        return $this->allLocales;
    }

    public function getRemainingLocales(): Collection
    {
        $locales = collect();

        foreach (ResourceBundle::getLocales('') as $locale) {
            if (
                Str::length($locale, 'UTF-8') === 2
                && $this->getAvailableLocales()->where('code', $locale)->isEmpty()
            ) {
                $locales[$locale] = $this->display($locale);
            }
        }

        return $locales->sortByDesc(
            fn ($_, $code) => in_array($code, [$this->currentLocale->code, 'tr', 'en', 'ar'])
        );
    }

    public function display(string $code): string
    {
        return Locale::getDisplayName($code, $this->currentLocale->code);
    }

    public function date(Carbon $date): string
    {
        $formatter = new IntlDateFormatter(
            $this->currentLocale->code,
            IntlDateFormatter::SHORT,
            IntlDateFormatter::NONE
        );

        return datefmt_format($formatter, $date->getTimestamp());
    }

    public function group(string $group, array $data): void
    {
        $this->groups[$group] = $data;
    }

    public function groups(): Collection
    {
        return $this->groups;
    }

    public function setCurrentLocale(string $locale): void
    {
        app()->setLocale($locale);

        $preferredLocale = $this->getPreferredLocale([
            ['code' => $locale],
            ['is_default' => true],
            [],
        ]);

        $this->currentLocale = $preferredLocale ?? $this->defaultLocale;
    }

    private function setDefaultLocale(): void
    {
        $this->defaultLocale = $this->getAvailableLocales()
            ->firstWhere('is_default', true) ?? (object) [
                'id' => -1,
                'code' => 'tr',
                'is_default' => 1,
            ];
    }

    private function getPreferredLocale(array $priorities): ?object
    {
        foreach ($priorities as $conditions) {
            $locale = $this->getAvailableLocales();

            foreach ($conditions as $key => $value) {
                $locale = $locale->where($key, $value);
            }

            if ($locale->isNotEmpty()) {
                return $locale->first();
            }
        }

        return null;
    }
}
