<?php

namespace App\Enums;

enum AttributeInputType: string
{
    case SELECT = 'select';
    case COLOR = 'color';
    case TEXT = 'text';

    private const LABELS = [
        self::SELECT->value => 'Lựa chọn',
        self::COLOR->value => 'Màu sắc',
        self::TEXT->value => 'Văn bản',
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
