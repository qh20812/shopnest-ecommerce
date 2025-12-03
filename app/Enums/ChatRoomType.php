<?php

namespace App\Enums;

enum ChatRoomType: string
{
    case CUSTOMER_SELLER = 'customer_seller';
    case CUSTOMER_SUPPORT = 'customer_support';

    private const LABELS = [
        self::CUSTOMER_SELLER->value => 'Khách hàng - Người bán',
        self::CUSTOMER_SUPPORT->value => 'Khách hàng - Hỗ trợ',
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
