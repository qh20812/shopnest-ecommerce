<?php

namespace App\Enums;

enum PromotionType: string
{
    case Percentage = 'percentage';
    case FixedAmount = 'fixed_amount';

    public function label(): string
    {
        return match($this) {
            self::Percentage => 'Giảm theo phần trăm (%)',
            self::FixedAmount => 'Giảm số tiền cố định',
        };
    }

    public function formatValue(float $value): string
    {
        return match($this) {
            self::Percentage => number_format($value, 0) . '%',
            self::FixedAmount => number_format($value, 0) . 'đ',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
