<?php

namespace Feadmin\Support;

use Feadmin\Enums\CurrencyEnum;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use JetBrains\PhpStorm\ArrayShape;
use NumberFormatter;

class Money implements Castable
{
    public static function castUsing(array $arguments): object|string
    {
        return new class($arguments) implements CastsAttributes
        {
            public function __construct(private readonly array $arguments = [])
            {
            }

            public function get($model, string $key, $value, array $attributes): Moneyable
            {
                if ($model->isFillable($this->arguments[0] ?? null)) {
                    $currency = $model->{$this->arguments[0]};
                } elseif (CurrencyEnum::tryFrom($this->arguments[0] ?? null)) {
                    $currency = $this->arguments[0];
                } else {
                    $currency = Currency::primary();
                }

                if (is_string($currency ?? null)) {
                    $currency = CurrencyEnum::tryFrom($currency);
                }

                return Money::of($value, $currency ?? null);
            }

            public function set($model, string $key, $value, array $attributes): float|int
            {
                if (is_string($value)) {
                    return Money::to($value);
                }

                if ($value instanceof Moneyable) {
                    return $value->get();
                }

                return $value;
            }
        };
    }

    public static function of(?int $amount, ?CurrencyEnum $currency = null): Moneyable
    {
        return new Moneyable($amount, $currency);
    }

    public static function from(int $amount): float
    {
        return $amount / self::digits();
    }

    public static function to(string|int|float|null $amount): ?int
    {
        if (is_null($amount)) {
            return null;
        }

        if (is_string($amount)) {
            $amount = preg_replace('/[^\d,.-]/', '', $amount);
            $amount = NumberFormatter::create(app()->getLocale(), NumberFormatter::DECIMAL)->parse($amount);
        }

        if ($amount === false) {
            return null;
        }

        return $amount * self::digits();
    }

    public static function toSpellout(int $amount): string
    {
        $formatter = NumberFormatter::create(app()->getLocale(), NumberFormatter::SPELLOUT);

        return $formatter->format((int) self::from($amount));
    }

    public static function format(int $amount, CurrencyEnum|bool $symbol = true, bool $abs = false): string
    {
        if ($abs === true) {
            $amount = abs($amount);
        }

        if ($symbol === true) {
            $symbol = Currency::current();
        }

        if ($symbol instanceof CurrencyEnum) {
            return NumberFormatter::create(app()->getLocale(), NumberFormatter::CURRENCY)
                ->formatCurrency(self::from($amount), $symbol->name);
        }

        return self::formatWithoutSymbol($amount);
    }

    public static function formatWithoutSymbol(int $amount): bool|string
    {
        $formatter = NumberFormatter::create(app()->getLocale(), NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return $formatter->format(self::from($amount));
    }

    public static function formatMany(array $amounts): array
    {
        return array_map(fn ($amount) => self::format($amount), $amounts);
    }

    public static function fromMany(array $amounts): array
    {
        return array_map(fn ($amount) => self::from($amount), $amounts);
    }

    #[ArrayShape(['decimal' => 'false|string', 'group' => 'false|string'])]
    public static function getSeparators(): array
    {
        $formatter = NumberFormatter::create(app()->getLocale(), NumberFormatter::DECIMAL);

        return [
            'decimal' => $formatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL),
            'group' => $formatter->getSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL),
        ];
    }

    public static function digits(): int
    {
        return 10_000;
    }
}
