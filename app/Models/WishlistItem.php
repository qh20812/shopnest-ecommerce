<?php
// app/Models/WishlistItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    protected $fillable = [
        'wishlist_id', 'product_id', 'variant_id', 'price_at_add',
        'current_price', 'variant_attributes', 'variant_name',
        'product_name', 'thumbnail', 'priority',
        'notify_price_drop', 'notify_back_in_stock'
    ];

    protected $casts = [
        'variant_attributes' => 'array',
        'price_at_add' => 'decimal:2',
        'current_price' => 'decimal:2',
        'notify_price_drop' => 'boolean',
        'notify_back_in_stock' => 'boolean',
        'added_at' => 'datetime',
    ];

    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Kiểm tra có giảm giá không
    public function getHasPriceDropAttribute(): bool
    {
        if (!$this->variant) return false;
        return $this->variant->display_price < $this->price_at_add;
    }

    // Phần trăm giảm giá
    public function getDiscountPercentAttribute(): ?int
    {
        if (!$this->has_price_drop) return null;
        return round((1 - $this->variant->display_price / $this->price_at_add) * 100);
    }
}