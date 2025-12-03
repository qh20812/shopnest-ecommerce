<?php

namespace App\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case PRODUCT_LINK = 'product_link';

    private const LABELS = [
        self::TEXT->value => 'Văn bản',
        self::IMAGE->value => 'Hình ảnh',
        self::PRODUCT_LINK->value => 'Link sản phẩm',
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
