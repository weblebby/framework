<?php

namespace Weblebby\Framework\Enums;

enum PostStatusEnum: int
{
    case PUBLISHED = 1;
    case PRIVATE = 2;
    case DRAFT = 0;

    public function label(): string
    {
        return match ($this) {
            self::PUBLISHED => __('Published'),
            self::PRIVATE => __('Private'),
            self::DRAFT => __('Draft'),
        };
    }

    public function hint(): string
    {
        return match ($this) {
            self::PUBLISHED => __('Visible to everyone.'),
            self::PRIVATE => __('Is not visible on your site but can be accessed via a link.'),
            self::DRAFT => __('Is not visible on your website and cannot be accessed via a link.'),
        };
    }
}
