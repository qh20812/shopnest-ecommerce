<?php

namespace App\Enums;

enum VehicleType: string
{
    case MOTORCYCLE = 'motorcycle';
    case CAR = 'car';
    case TRUCK = 'truck';

    private const LABELS = [
        self::MOTORCYCLE->value => 'Xe máy',
        self::CAR->value => 'Ô tô',
        self::TRUCK->value => 'Xe tải',
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
