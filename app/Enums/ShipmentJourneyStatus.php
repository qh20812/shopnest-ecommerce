<?php

namespace App\Enums;

enum ShipmentJourneyStatus: string
{
    case PICKED_UP = 'picked_up';
    case AT_HUB = 'at_hub';
    case IN_TRANSIT = 'in_transit';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';

    private const LABELS = [
        self::PICKED_UP->value => 'Đã lấy hàng',
        self::AT_HUB->value => 'Tại trung tâm',
        self::IN_TRANSIT->value => 'Đang vận chuyển',
        self::OUT_FOR_DELIVERY->value => 'Đang giao hàng',
        self::DELIVERED->value => 'Đã giao',
        self::FAILED->value => 'Thất bại',
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
