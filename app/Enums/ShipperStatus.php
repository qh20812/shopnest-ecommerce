<?php

namespace App\Enums;

enum ShipperStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Busy = 'busy';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Đang hoạt động',
            self::Inactive => 'Ngừng hoạt động',
            self::Busy => 'Đang bận',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Active => 'success',
            self::Inactive => 'secondary',
            self::Busy => 'warning',
        };
    }

    public function canAssignOrder(): bool
    {
        return $this === self::Active;
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
