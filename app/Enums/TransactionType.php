<?php

namespace App\Enums;

enum TransactionType: string
{
    case Payment = 'payment';
    case Refund = 'refund';

    public function label(): string
    {
        return match($this) {
            self::Payment => 'Thanh toán',
            self::Refund => 'Hoàn tiền',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Payment => 'success',
            self::Refund => 'warning',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
