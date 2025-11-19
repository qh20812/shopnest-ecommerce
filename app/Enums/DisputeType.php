<?php

namespace App\Enums;

enum DisputeType: string
{
    case NotReceived = 'not_received';
    case DamagedProduct = 'damaged_product';
    case WrongItem = 'wrong_item';
    case Other = 'other';

    public function label(): string
    {
        return match($this) {
            self::NotReceived => 'Không nhận được hàng',
            self::DamagedProduct => 'Hàng bị lỗi/vỡ',
            self::WrongItem => 'Giao sai hàng',
            self::Other => 'Khác',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::NotReceived => '📦❌',
            self::DamagedProduct => '💔',
            self::WrongItem => '🔄',
            self::Other => '❓',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
