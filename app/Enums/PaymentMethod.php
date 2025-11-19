<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case COD = 'cod';
    case Stripe = 'stripe';
    case Momo = 'momo';
    case VNPay = 'vnpay';
    case PayPal = 'paypal';

    public function label(): string
    {
        return match($this) {
            self::COD => 'Thanh toán khi nhận hàng (COD)',
            self::Stripe => 'Thẻ tín dụng/ghi nợ (Stripe)',
            self::Momo => 'Ví MoMo',
            self::VNPay => 'VNPay',
            self::PayPal => 'PayPal',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::COD => '💵',
            self::Stripe => '💳',
            self::Momo => '🟣',
            self::VNPay => '🔵',
            self::PayPal => '🅿️',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
