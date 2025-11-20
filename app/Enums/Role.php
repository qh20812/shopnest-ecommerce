<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case SELLER = 'seller';
    case SHIPPER = 'shipper';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Quản trị viên',
            self::CUSTOMER => 'Khách hàng',
            self::SELLER => 'Người bán',
            self::SHIPPER => 'Người giao hàng',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'red',
            self::CUSTOMER => 'blue',
            self::SELLER => 'green',
            self::SHIPPER => 'orange',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ADMIN => 'fas fa-crown',
            self::CUSTOMER => 'fas fa-user',
            self::SELLER => 'fas fa-store',
            self::SHIPPER => 'fas fa-truck',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ADMIN => 'Quản lý toàn bộ hệ thống',
            self::CUSTOMER => 'Người mua hàng',
            self::SELLER => 'Người bán sản phẩm',
            self::SHIPPER => 'Người giao hàng',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($role) => [
            $role->value => $role->label()
        ])->toArray();
    }
}
