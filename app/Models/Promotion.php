<?php
// app/Models/Promotion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promotion extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'max_discount_amount',
        'min_order_amount',
        'usage_limit_total',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'ends_at',
        'is_active',
        'applies_to'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'applies_to' => 'array',
    ];

    // Kiểm tra mã có hợp lệ không
    public function isValid(?Carbon $now = null, ?int $userId = null, float $orderAmount = 0): bool
    {
        $now = $now ?? now();

        if (!$this->is_active) return false;
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        if ($this->usage_limit_total && $this->used_count >= $this->usage_limit_total) return false;
        if ($orderAmount < ($this->min_order_amount ?? 0)) return false;

        if ($userId) {
            $used = $this->users()->where('user_id', $userId)->first()?->pivot->used_count ?? 0;
            if ($used >= $this->usage_limit_per_user) return false;
        }

        return true;
    }

    // Tính tiền giảm
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);
            if ($this->max_discount_amount) {
                $discount = min($discount, $this->max_discount_amount);
            }
            return round($discount, 0);
        }

        return min($this->value, $subtotal);
    }

    // Quan hệ người dùng đã sử dụng
    public function users()
    {
        return $this->belongsToMany(User::class, 'promotion_user')
            ->withPivot('used_count', 'last_used_at')
            ->withTimestamps();
    }

    // Scope: mã còn hiệu lực
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }
}
