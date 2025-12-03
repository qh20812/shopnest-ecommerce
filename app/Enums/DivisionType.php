<?php

namespace App\Enums;

enum DivisionType: string
{
    case PROVINCE = 'province';
    case WARD = 'ward';

    private const LABELS = [
        self::PROVINCE->value => 'Tỉnh/Thành phố',
        self::WARD->value => 'Xã/Phường',
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
