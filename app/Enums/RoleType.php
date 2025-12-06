<?php

namespace App\Enums;

enum RoleType: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case SELLER = 'seller';
    case SHIPPER = 'shipper';

    private const LABELS = [
        self::ADMIN->value => 'Quản trị viên',
        self::CUSTOMER->value => 'Khách hàng',
        self::SELLER->value => 'Người bán',
        self::SHIPPER->value => 'Nhân viên giao hàng',
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
