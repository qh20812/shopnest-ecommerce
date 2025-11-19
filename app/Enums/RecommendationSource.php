<?php

namespace App\Enums;

enum RecommendationSource: string
{
    case Bestseller = 'bestseller';
    case ViewedTogether = 'viewed_together';
    case BoughtTogether = 'bought_together';
    case SimilarCategory = 'similar_category';
    case Personalized = 'personalized';
    case NewArrival = 'new_arrival';
    case PriceDrop = 'price_drop';
    case BackInStock = 'back_in_stock';

    public function label(): string
    {
        return match($this) {
            self::Bestseller => 'Bán chạy nhất',
            self::ViewedTogether => 'Khách xem cùng',
            self::BoughtTogether => 'Mua cùng',
            self::SimilarCategory => 'Cùng danh mục',
            self::Personalized => 'Gợi ý riêng',
            self::NewArrival => 'Hàng mới',
            self::PriceDrop => 'Giảm giá mạnh',
            self::BackInStock => 'Có hàng trở lại',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Bestseller => '🔥',
            self::ViewedTogether => '👀',
            self::BoughtTogether => '🛒',
            self::SimilarCategory => '📂',
            self::Personalized => '⭐',
            self::NewArrival => '🆕',
            self::PriceDrop => '💰',
            self::BackInStock => '✅',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
