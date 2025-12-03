<?php

namespace App\Enums;

enum Theme: string
{
    case LIGHT = 'light';
    case DARK = 'dark';
    case AUTO = 'auto';

    private const LABELS = [
        self::LIGHT->value => 'Sáng',
        self::DARK->value => 'Tối',
        self::AUTO->value => 'Tự động',
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
