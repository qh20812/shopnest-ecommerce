<?php
// app/Models/CartItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'variant_id',
        'quantity',
        'unit_price',
        'sale_price',
        'final_price',
        'variant_attributes',
        'product_name',
        'variant_name',
        'thumbnail'
    ];

    protected $casts = [
        'variant_attributes' => 'array',
        'unit_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    // Người dùng (nếu đăng nhập)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Variant sản phẩm
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Tăng số lượng
    public function increase(int $qty = 1): void
    {
        $this->quantity += $qty;
        $this->save();
    }

    // Giảm số lượng
    public function decrease(int $qty = 1): void
    {
        if ($this->quantity <= $qty) {
            $this->delete();
        } else {
            $this->quantity -= $qty;
            $this->save();
        }
    }

    // Tổng tiền của item này
    public function getSubtotalAttribute(): float
    {
        return $this->final_price * $this->quantity;
    }

    // Kiểm tra còn hàng không (so với tồn kho hiện tại)
    public function getIsAvailableAttribute(): bool
    {
        if (!$this->variant) return false;
        return $this->variant->available_quantity >= $this->quantity;
    }

    // Scope: giỏ hàng của user đăng nhập
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope: giỏ hàng của khách vãng lai
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    // Lấy giỏ hàng hiện tại (user hoặc session)
    public static function currentCart($user = null, $sessionId = null)
    {
        $query = self::query();

        if ($user) {
            $query->forUser($user->id);
        } elseif ($sessionId) {
            $query->forSession($sessionId);
        }

        return $query->with('variant.product');
    }
}
