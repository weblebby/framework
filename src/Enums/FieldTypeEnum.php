<?php

namespace Feadmin\Enums;

enum FieldTypeEnum: string
{
    case TEXT = 'text';
    case TEL = 'tel';
    case NUMBER = 'number';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case TEXT_AREA = 'text_area';
    case RICH_TEXT = 'rich_text';
    case IMAGE = 'image';

    public static function uploadables(): array
    {
        return [
            self::IMAGE,
        ];
    }

    public static function htmlables(): array
    {
        return [
            self::RICH_TEXT,
        ];
    }

    public function isUploadable(): bool
    {
        return in_array($this->value, self::uploadables());
    }

    public function isHtmlable(): bool
    {
        return in_array($this->value, self::htmlables());
    }
}
