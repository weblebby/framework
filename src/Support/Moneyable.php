<?php

namespace Feadmin\Support;

use Feadmin\Enums\CurrencyEnum;
use Illuminate\Contracts\Support\DeferringDisplayableValue;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\JsonEncodingException;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

class Moneyable implements DeferringDisplayableValue, Jsonable, JsonSerializable
{
    protected mixed $amount;

    protected ?CurrencyEnum $currency;

    public function __construct($amount, ?CurrencyEnum $currency = null)
    {
        $this->amount = $amount ?: 0;
        $this->currency = $currency ?: Currency::primary();
    }

    public function currency(): ?CurrencyEnum
    {
        return $this->currency;
    }

    public function get(): int
    {
        return $this->amount ?: 0;
    }

    public function toSpellout(): string
    {
        return Money::toSpellout($this->amount);
    }

    public function toInt(): int
    {
        return Money::to($this->amount);
    }

    public function fromInt(): float
    {
        return Money::from($this->amount);
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function isFree(): bool
    {
        return $this->amount === 0;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function isFreeOrNegative(): bool
    {
        return $this->amount <= 0;
    }

    public function format(bool $symbol = true, bool $abs = false): string
    {
        return Money::format(
            $this->amount,
            symbol: $symbol ? $this->currency : false,
            abs: $abs,
        );
    }

    public function convert(?CurrencyEnum $currency = null): static
    {
        if (is_null($currency)) {
            $currency = Currency::current();
        }

        if ($this->currency() === $currency) {
            return $this;
        }

        return new static(
            Currency::convert($this->amount, from: $this->currency(), to: $currency),
            $currency
        );
    }

    public function set(Moneyable|int|null $money): static
    {
        if (is_null($money)) {
            return $this;
        }

        return new static($money, $this->currency);
    }

    public function add(Moneyable|int|null $money): static
    {
        if (is_null($money)) {
            return $this;
        }

        return new static(
            $this->amount + ($money instanceof static ? $money->get() : $money),
            $this->currency
        );
    }

    public function sub(Moneyable|int|null $money): static
    {
        if (is_null($money)) {
            return $this;
        }

        return new static(
            $this->amount - ($money instanceof static ? $money->get() : $money),
            $this->currency
        );
    }

    public function mul(int|float $multiplier): static
    {
        return new static($this->amount * $multiplier, $this->currency);
    }

    public function div(int|float $multiplier): static
    {
        return new static($this->amount / $multiplier, $this->currency);
    }

    public function min(int|float $number): static
    {
        return new static(max($this->amount, $number), $this->currency);
    }

    public function max(int|float $number): static
    {
        return new static(min($this->amount, $number), $this->currency);
    }

    public function abs(): static
    {
        return new static(abs($this->amount), $this->currency);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    #[ArrayShape(['amount' => 'int|mixed', 'format' => 'string', 'format_with_currency' => 'string'])]
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'format' => $this->format(symbol: false),
            'format_with_currency' => $this->format(),
        ];
    }

    public function toJson($options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }

        return $json;
    }

    public function resolveDisplayableValue(): string
    {
        return $this->format();
    }
}
