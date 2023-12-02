<?php

namespace Feadmin\Support;

use Feadmin\Enums\CurrencyEnum;
use NumberFormatter;

class Currency
{
    protected static CurrencyEnum $currentCurrency;

    public static function primary(): CurrencyEnum
    {
        return CurrencyEnum::USD;
    }

    public static function setCurrentCurrency(CurrencyEnum $currency): void
    {
        self::$currentCurrency = $currency;
    }

    public static function current(): CurrencyEnum
    {
        return self::$currentCurrency;
    }

    public static function detect(): CurrencyEnum
    {
        $formatter = NumberFormatter::create(app()->getLocale(), NumberFormatter::CURRENCY);
        $currency = $formatter->getTextAttribute(NumberFormatter::CURRENCY_CODE);

        if ($enum = CurrencyEnum::tryFrom($currency)) {
            return $enum;
        }

        return self::primary();
    }

    public static function convert(
        int $amount,
        CurrencyEnum $from = null,
        CurrencyEnum $to = null
    ): float {
        if (is_null($from)) {
            $from = self::primary();
        }

        if (is_null($to)) {
            $to = self::current();
        }

        if ($from === $to) {
            return $amount;
        }

        if ($to === self::primary()) {
            return $amount / ($from->rate() / self::digits());
        }

        return $amount * ($to->rate() / self::digits());
    }

    public static function digits(): int
    {
        return 1_000_000;
    }
}
