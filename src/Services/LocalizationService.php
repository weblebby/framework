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

    private array $translations = [];

    public function __construct()
    {
        $this->load();
    }

    public function load(): void
    {
        $this->allLocales = collect(config('feadmin.all_locales'))
            ->map(function ($locale, $code) {
                $locale['code'] = $code;

                return $locale;
            })
            ->sortBy('name');

        $this->availableLocales = DB::table('locales')->get();

        $this->setDefaultLocale();
        $this->setTranslations();
    }

    public function getDefaultLocale(): object
    {
        return $this->defaultLocale;
    }

    public function getDefaultLocaleCode(): string
    {
        return $this->getDefaultLocale()->code;
    }

    public function getDefaultLocaleId(): int
    {
        return $this->getDefaultLocale()->id;
    }

    public function getCurrentLocale(): object
    {
        return $this->currentLocale;
    }

    public function getCurrentLocaleCode(): string
    {
        return $this->getCurrentLocale()->code;
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

    public function getTranslations(): array
    {
        return $this->translations;
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

    public function setCurrentLocale(object $locale): void
    {
        $this->currentLocale = $locale;
    }

    public function getPreferredLocale(array $priorities): ?object
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

    private function setTranslations(): void
    {
        $translations = trans('*', locale: $this->getDefaultLocaleCode());

        $this->translations = $translations === '*' ? [] : $translations;
    }

    private function setDefaultLocale(): void
    {
        $this->defaultLocale = $this->getAvailableLocales()->firstWhere('is_default', true)
            ?? (object) [
                'id' => -1,
                'code' => app()->getLocale(),
                'is_default' => 1,
            ];

        $this->currentLocale = $this->defaultLocale;
    }
}
