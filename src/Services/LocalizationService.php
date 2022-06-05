<?php

namespace Feadmin\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use IntlDateFormatter;
use Locale;
use NumberFormatter;
use Symfony\Component\Intl\Locales;

class LocalizationService
{
    private Collection $supportedLocales;

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
        $this->loadAllLocales();
        $this->loadSupportedLocales();
        $this->setDefaultLocale();
        $this->loadTranslations();
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
        return $this->getSupportedLocales()->firstWhere('code', $code);
    }

    public function getSupportedLocales(): Collection
    {
        return $this->supportedLocales;
    }

    public function getAllLocales(): Collection
    {
        return $this->allLocales;
    }

    public function getRemainingLocales(): Collection
    {
        $locales = collect();

        foreach ($this->allLocales as $locale) {
            if ($this->getSupportedLocales()->where('code', $locale['code'])->isEmpty()) {
                $locales[$locale['code']] = $this->display($locale['code']);
            }
        }

        return $locales;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function currency(string $code): string
    {
        $formatter = new NumberFormatter($code, NumberFormatter::CURRENCY);

        return $formatter->getTextAttribute(NumberFormatter::CURRENCY_CODE);
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
            $locale = $this->getSupportedLocales();

            foreach ($conditions as $key => $value) {
                $locale = $locale->where($key, $value);
            }

            if ($locale->isNotEmpty()) {
                return $locale->first();
            }
        }

        return null;
    }

    public function loadAllLocales(): void
    {
        $this->allLocales = collect(Locales::getNames())
            ->map(function ($locale, $code) {
                return [
                    'code' => str_replace('_', '-', $code),
                    'name' => $locale,
                ];
            })
            ->values();
    }

    private function setDefaultLocale(): void
    {
        $this->defaultLocale = $this->getSupportedLocales()
            ->firstWhere('is_default', true) ?? (object) [
                'id' => -1,
                'code' => app()->getLocale(),
                'is_default' => 1,
            ];

        $this->currentLocale = $this->defaultLocale;
    }

    private function loadSupportedLocales(): void
    {
        $this->supportedLocales = DB::table('locales')->get();
    }

    private function loadTranslations(): void
    {
        $translations = trans('*', locale: $this->getDefaultLocaleCode());

        $this->translations = $translations === '*' ? [] : $translations;
    }
}
