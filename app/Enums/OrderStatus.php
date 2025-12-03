<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPING = 'shipping';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    private const LABELS = [
        self::PENDING->value => 'Chờ xác nhận',
        self::CONFIRMED->value => 'Đã xác nhận',
        self::PROCESSING->value => 'Đang xử lý',
        self::SHIPPING->value => 'Đang giao hàng',
        self::DELIVERED->value => 'Đã giao',
        self::CANCELLED->value => 'Đã hủy',
        self::REFUNDED->value => 'Đã hoàn tiền',
    ];

    /**
     * Get the label for the enum case
     */
    public function label(): string
    {
        return self::LABELS[$this->value];
    }

    /**
     * Get all enum values with their labels
     */
    public static function options(): array
    {
        return array_map(
            fn(self $enum) => ['value' => $enum->value, 'label' => $enum->label()],
            self::cases()
        );
    }
}
