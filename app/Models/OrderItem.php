<?php
// app/Models/OrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'variant_id',
        'quantity',
        'unit_price',
        'sale_price',
        'final_price',
        'total_price',
        'product_name',
        'variant_name',
        'variant_attributes',
        'thumbnail'
    ];

    protected $casts = [
        'variant_attributes' => 'array',
        'unit_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Lấy tên hiển thị đẹp
    public function getDisplayNameAttribute(): string
    {
        return $this->variant_name
            ? "{$this->product_name} - {$this->variant_name}"
            : $this->product_name;
    }
}
