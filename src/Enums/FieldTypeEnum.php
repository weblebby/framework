<?php

namespace Feadmin\Enums;

enum FieldTypeEnum: string
{
    case REPEATED = 'repeated';
    case GROUPED = 'grouped';
    case PARAGRAPH = 'paragraph';
    case TEXT = 'text';
    case TEL = 'tel';
    case NUMBER = 'number';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case TEXT_AREA = 'text_area';
    case RICH_TEXT = 'rich_text';
    case IMAGE = 'image';

    public static function translatables(): array
    {
        return [
            self::TEXT,
            self::TEL,
            self::NUMBER,
            self::TEXT_AREA,
            self::RICH_TEXT,
        ];
    }

    public static function informationals(): array
    {
        return [
            self::PARAGRAPH,
            self::REPEATED,
            self::GROUPED,
        ];
    }

    public static function labelFree(): array
    {
        return [
            self::CHECKBOX,
            self::RADIO,
        ];
    }

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

    public function isInformational(): bool
    {
        return in_array($this, self::informationals());
    }

    public function isTranslatable(): bool
    {
        return in_array($this, self::translatables());
    }

    public function isEditable(): bool
    {
        return ! $this->isInformational();
    }

    public function isLabelFree(): bool
    {
        return in_array($this, self::labelFree());
    }

    public function isUploadable(): bool
    {
        return in_array($this, self::uploadables());
    }

    public function isHtmlable(): bool
    {
        return in_array($this, self::htmlables());
    }

    public function isValueless(): bool
    {
        return $this->isInformational() || $this->isUploadable();
    }
}
