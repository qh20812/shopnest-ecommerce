<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case Pending = 'pending';
    case InReview = 'in_review';
    case Resolved = 'resolved';
    case Rejected = 'rejected';
    case Escalated = 'escalated';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Chờ xử lý',
            self::InReview => 'Đang xem xét',
            self::Resolved => 'Đã giải quyết',
            self::Rejected => 'Từ chối',
            self::Escalated => 'Chuyển cấp cao',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::InReview => 'info',
            self::Resolved => 'success',
            self::Rejected => 'danger',
            self::Escalated => 'purple',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
