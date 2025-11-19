<?php

namespace App\Enums;

enum OrderReturnStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Processing = 'processing';
    case Completed = 'completed';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Chờ xử lý',
            self::Approved => 'Đã duyệt',
            self::Rejected => 'Từ chối',
            self::Processing => 'Đang xử lý',
            self::Completed => 'Hoàn thành',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Approved => 'info',
            self::Rejected => 'danger',
            self::Processing => 'primary',
            self::Completed => 'success',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
