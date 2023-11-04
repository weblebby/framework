<?php

namespace Feadmin\Support;

use NumberFormatter;

class Number
{
    public static function format(int|float|string $number): bool|string
    {
        return NumberFormatter::create(
            app()->getLocale(),
            NumberFormatter::DECIMAL
        )->format($number);
    }
}
