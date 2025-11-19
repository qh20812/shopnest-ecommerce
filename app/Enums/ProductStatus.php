<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Nháp',
            self::Published => 'Đã xuất bản',
            self::Archived => 'Đã lưu trữ',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft => 'secondary',
            self::Published => 'success',
            self::Archived => 'warning',
        };
    }

    public function isVisible(): bool
    {
        return $this === self::Published;
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
