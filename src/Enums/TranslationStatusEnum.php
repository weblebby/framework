<?php

namespace Weblebby\Framework\Enums;

enum TranslationStatusEnum: int
{
    case TRANSLATED = 1;
    case NOT_TRANSLATED = 2;

    public function label(): string
    {
        return match ($this) {
            self::TRANSLATED => __('Translated'),
            self::NOT_TRANSLATED => __('Untranslated'),
        };
    }
}
