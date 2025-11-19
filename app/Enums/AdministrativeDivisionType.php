<?php

namespace App\Enums;

enum AdministrativeDivisionType: string
{
    case Province = 'province';
    case Ward = 'ward';

    public function label(): string
    {
        return match($this) {
            self::Province => 'Tỉnh/Thành phố',
            self::Ward => 'Xã/Phường/Thị trấn',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
