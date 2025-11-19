<?php

namespace App\Enums;

enum LoyaltyPointType: string
{
    case Earn = 'earn';
    case Spend = 'spend';
    case Expire = 'expire';
    case Adjust = 'adjust';

    public function label(): string
    {
        return match($this) {
            self::Earn => 'Tích điểm',
            self::Spend => 'Tiêu điểm',
            self::Expire => 'Hết hạn',
            self::Adjust => 'Điều chỉnh',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Earn => 'success',
            self::Spend => 'warning',
            self::Expire => 'danger',
            self::Adjust => 'info',
        };
    }

    public function isPositive(): bool
    {
        return in_array($this, [self::Earn, self::Adjust]);
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
