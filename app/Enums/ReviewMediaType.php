<?php

namespace App\Enums;

enum ReviewMediaType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';

    private const LABELS = [
        self::IMAGE->value => 'Hình ảnh',
        self::VIDEO->value => 'Video',
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
