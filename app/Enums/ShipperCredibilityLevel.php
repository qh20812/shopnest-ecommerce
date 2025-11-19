<?php

namespace App\Enums;

enum ShipperCredibilityLevel: string
{
    case Excellent = 'excellent';
    case Good = 'good';
    case Average = 'average';
    case Warning = 'warning';
    case Poor = 'poor';

    public function label(): string
    {
        return match($this) {
            self::Excellent => 'Xuất sắc',
            self::Good => 'Tốt',
            self::Average => 'Trung bình',
            self::Warning => 'Cảnh báo',
            self::Poor => 'Kém',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Excellent => 'success',
            self::Good => 'primary',
            self::Average => 'info',
            self::Warning => 'warning',
            self::Poor => 'danger',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::Excellent => '⭐⭐⭐⭐⭐',
            self::Good => '⭐⭐⭐⭐',
            self::Average => '⭐⭐⭐',
            self::Warning => '⭐⭐',
            self::Poor => '⭐',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
