<?php

namespace App\Enums;

enum ReturnStatus: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case RECEIVED = 'received';
    case REFUNDED = 'refunded';

    private const LABELS = [
        self::REQUESTED->value => 'Yêu cầu trả hàng',
        self::APPROVED->value => 'Đã chấp nhận',
        self::REJECTED->value => 'Đã từ chối',
        self::RECEIVED->value => 'Đã nhận hàng',
        self::REFUNDED->value => 'Đã hoàn tiền',
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
