<?php

namespace Feadmin\Support;

use BadMethodCallException;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use IntlDateFormatter;

/**
 * @method static $this short(DateTimeInterface|string|null $date, string|int $timeType = null): ?string
 * @method static $this medium(DateTimeInterface|string|null $date, string|int $timeType = null): ?string
 * @method static $this long(DateTimeInterface|string|null $date, string|int $timeType = null): ?string
 * @method static $this full(DateTimeInterface|string|null $date, string|int $timeType = null): ?string
 */
class Date
{
    protected static array $dateTypes = [
        'short' => IntlDateFormatter::SHORT,
        'medium' => IntlDateFormatter::MEDIUM,
        'long' => IntlDateFormatter::LONG,
        'full' => IntlDateFormatter::FULL,
    ];

    public static function __callStatic(string $method, array $args): ?string
    {
        $date = $args[0] ?? null;

        if (! $date instanceof DateTimeInterface && ! is_string($date)) {
            return null;
        }

        $dateType = self::$dateTypes[$method] ?? null;

        if (is_null($dateType)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s() does not exist.',
                static::class,
                $method
            ));
        }

        return self::format($args[0], $dateType, $args[1] ?? null);
    }

    public static function format(DateTimeInterface|string|null $date, string|int $dateType, string|int|null $timeType = null): ?string
    {
        if (is_null($date)) {
            return null;
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if (self::$dateTypes[$dateType] ?? null) {
            $dateType = self::$dateTypes[$dateType];
        }

        if (self::$dateTypes[$timeType] ?? null) {
            $timeType = self::$dateTypes[$timeType];
        }

        $formatter = new IntlDateFormatter(self::locale(), $dateType, $timeType ?? IntlDateFormatter::NONE);

        return datefmt_format($formatter, $date->getTimestamp());
    }

    private static function locale(): string
    {
        return app()->getLocale();
    }
}
