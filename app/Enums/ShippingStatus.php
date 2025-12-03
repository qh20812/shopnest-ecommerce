<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case PENDING = 'pending';
    case PICKED_UP = 'picked_up';
    case IN_TRANSIT = 'in_transit';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case RETURNED = 'returned';

    private const LABELS = [
        self::PENDING->value => 'Chờ lấy hàng',
        self::PICKED_UP->value => 'Đã lấy hàng',
        self::IN_TRANSIT->value => 'Đang vận chuyển',
        self::OUT_FOR_DELIVERY->value => 'Đang giao hàng',
        self::DELIVERED->value => 'Đã giao',
        self::FAILED->value => 'Giao thất bại',
        self::RETURNED->value => 'Đã trả lại',
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
