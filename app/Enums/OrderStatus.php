<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Returned = 'returned';

    /**
     * Lấy label tiếng Việt cho hiển thị
     */
    public function label(): string
    {
        return match($this) {
            self::Pending => 'Chờ xác nhận',
            self::Confirmed => 'Đã xác nhận',
            self::Processing => 'Đang xử lý',
            self::Shipped => 'Đang giao hàng',
            self::Delivered => 'Đã giao hàng',
            self::Cancelled => 'Đã hủy',
            self::Returned => 'Đã trả hàng',
        };
    }

    /**
     * Màu sắc để hiển thị badge
     */
    public function color(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Processing => 'primary',
            self::Shipped => 'purple',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
            self::Returned => 'secondary',
        };
    }

    /**
     * Kiểm tra có thể hủy đơn không
     */
    public function canCancel(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed]);
    }

    /**
     * Kiểm tra có thể trả hàng không
     */
    public function canReturn(): bool
    {
        return $this === self::Delivered;
    }

    /**
     * Lấy tất cả giá trị dưới dạng array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
