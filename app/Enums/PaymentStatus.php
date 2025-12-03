<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case PARTIALLY_REFUNDED = 'partially_refunded';
    case REFUNDED = 'refunded';

    private const LABELS = [
        self::UNPAID->value => 'Chưa thanh toán',
        self::PAID->value => 'Đã thanh toán',
        self::PARTIALLY_REFUNDED->value => 'Hoàn tiền một phần',
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
