<?php

namespace Weblebby\Framework\Enums;

enum PostStatusEnum: int
{
    case DRAFT = 0;
    case PUBLISHED = 1;
    case PRIVATE = 2;

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => __('Taslak'),
            self::PUBLISHED => __('Yayında'),
            self::PRIVATE => __('Liste Dışı'),
        };
    }

    public function hint(): string
    {
        return match ($this) {
            self::DRAFT => __('Sitenizde görünmez ve linkle erişilemez.'),
            self::PUBLISHED => __('Herkes tarafından görülebilir.'),
            self::PRIVATE => __('Sitenizde görünmez ama linkle erişilebilir.'),
        };
    }
}
