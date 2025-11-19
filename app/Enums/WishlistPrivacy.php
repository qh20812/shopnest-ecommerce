<?php

namespace App\Enums;

enum WishlistPrivacy: string
{
    case Private = 'private';
    case Shared = 'shared';
    case Public = 'public';

    public function label(): string
    {
        return match($this) {
            self::Private => 'Riêng tư',
            self::Shared => 'Chia sẻ',
            self::Public => 'Công khai',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Private => '🔒',
            self::Shared => '👥',
            self::Public => '🌍',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::Private => 'Chỉ bạn có thể xem',
            self::Shared => 'Chia sẻ với người có link',
            self::Public => 'Ai cũng có thể xem',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
