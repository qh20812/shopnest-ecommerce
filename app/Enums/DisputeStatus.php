<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case OPEN = 'open';
    case IN_REVIEW = 'in_review';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    private const LABELS = [
        self::OPEN->value => 'Đang mở',
        self::IN_REVIEW->value => 'Đang xem xét',
        self::RESOLVED->value => 'Đã giải quyết',
        self::CLOSED->value => 'Đã đóng',
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
