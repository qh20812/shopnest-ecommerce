<?php

namespace App\Enums;

enum ProductStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case OUT_OF_STOCK = 'out_of_stock';

    private const LABELS = [
        self::DRAFT->value => 'Nháp',
        self::ACTIVE->value => 'Đang hoạt động',
        self::INACTIVE->value => 'Tạm ngưng',
        self::OUT_OF_STOCK->value => 'Hết hàng',
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
