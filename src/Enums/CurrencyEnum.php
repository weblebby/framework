<?php

namespace Weblebby\Framework\Enums;

use Symfony\Component\Intl\Currencies;
use Weblebby\Framework\Support\Currency;
use Weblebby\Framework\Support\CurrencyRate;

enum CurrencyEnum: string
{
    case USD = 'USD';
    case TRY = 'TRY';

    public static function sortedCases(): array
    {
        return collect(self::cases())->sortByDesc(fn ($case) => $case === Currency::current() || Currency::primary())->toArray();
    }

    public static function casesWithoutPrimary(): array
    {
        return array_filter(self::cases(), fn (self $currency) => $currency !== Currency::primary());
    }

    public function title(): string
    {
        return Currencies::getName($this->name);
    }

    public function symbol(): string
    {
        return Currencies::getSymbol($this->name);
    }

    public function fractionDigits(): int
    {
        return Currencies::getFractionDigits($this->name);
    }

    public function fraction(): string
    {
        return match ($this) {
            self::USD => __('cent'),
            self::TRY => __('penny'),
        };
    }

    public function rate(): ?int
    {
        return app(CurrencyRate::class)->find($this)?->rate;
    }
}
