<?php
// app/Models/ProductVariant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'price',
        'sale_price',
        'stock_quantity',
        'reserved_quantity',
        'attributes',
        'image',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'attributes' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Quan hệ với sản phẩm
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Giá hiển thị cuối cùng (ưu tiên sale_price)
    public function getDisplayPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    // Kiểm tra còn hàng không
    public function getInStockAttribute(): bool
    {
        return ($this->stock_quantity - $this->reserved_quantity) > 0;
    }

    // Số lượng khả dụng để mua
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    // Lấy tên thuộc tính đẹp (VD: "Màu: Đen | Dung lượng: 128GB")
    public function getAttributeLabelAttribute(): string
    {
        if (empty($this->attributes)) return '';

        return collect($this->attributes)->map(function ($value, $key) {
            $key = ucfirst(str_replace('_', ' ', $key));
            return "$key: $value";
        })->implode(' | ');
    }

    // Scope: chỉ lấy variant đang bán
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: có hàng
    public function scopeInStock($query)
    {
        return $query->whereRaw('stock_quantity > reserved_quantity');
    }
}