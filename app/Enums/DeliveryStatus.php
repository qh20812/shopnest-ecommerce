<?php

namespace App\Enums;

enum DeliveryStatus: string
{
    case Assigned = 'assigned';
    case PickedUp = 'picked_up';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Returned = 'returned';

    public function label(): string
    {
        return match($this) {
            self::Assigned => 'Đã phân công',
            self::PickedUp => 'Đã lấy hàng',
            self::InTransit => 'Đang giao',
            self::Delivered => 'Đã giao thành công',
            self::Failed => 'Giao thất bại',
            self::Returned => 'Đã trả về kho',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Assigned => 'info',
            self::PickedUp => 'primary',
            self::InTransit => 'warning',
            self::Delivered => 'success',
            self::Failed => 'danger',
            self::Returned => 'secondary',
        };
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::Delivered, self::Returned]);
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
