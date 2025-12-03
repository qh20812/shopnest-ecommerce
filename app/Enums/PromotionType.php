<?php

namespace App\Enums;

enum PromotionType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED_AMOUNT = 'fixed_amount';
    case FREE_SHIPPING = 'free_shipping';
    case BUY_X_GET_Y = 'buy_x_get_y';

    private const LABELS = [
        self::PERCENTAGE->value => 'Giảm theo phần trăm',
        self::FIXED_AMOUNT->value => 'Giảm giá cố định',
        self::FREE_SHIPPING->value => 'Miễn phí vận chuyển',
        self::BUY_X_GET_Y->value => 'Mua X tặng Y',
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
