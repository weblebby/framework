<?php

namespace Weblebby\Framework\Enums;

enum UnitEnum: int
{
    case MONTH = -1;
    case YEAR = -2;

    public function title(): string
    {
        return match ($this) {
            self::MONTH => __('Month'),
            self::YEAR => __('Year'),
        };
    }

    public function periodLabel(): string
    {
        return match ($this) {
            self::MONTH => __('Monthly'),
            self::YEAR => __('Annual'),
        };
    }

    public function period(): ?string
    {
        return match ($this) {
            self::MONTH => 'P1M',
            self::YEAR => 'P1Y',
        };
    }

    public function toDays(): ?int
    {
        return match ($this) {
            self::MONTH => 30,
            self::YEAR => 365,
        };
    }

    public function toArray(): array
    {
        return [
            'key' => $this->name,
            'value' => $this->value,
            'title' => $this->title(),
        ];
    }
}
