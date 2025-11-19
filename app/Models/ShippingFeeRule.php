<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingFeeRule extends Model
{
    protected $fillable = [
        'name',
        'description',
        'min_order_amount',
        'max_order_amount',
        'shipping_fee',
        'provinces',
        'apply_to_all_provinces',
        'priority',
        'is_active',
        'is_freeship_rule'
    ];

    protected $casts = [
        'provinces' => 'array',
        'min_order_amount' => 'decimal:2',
        'max_order_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'is_freeship_rule' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderByDesc('priority');
    }

    // Kiểm tra rule có áp dụng cho tỉnh này không
    public function appliesToProvince(string $provinceName): bool
    {
        if ($this->apply_to_all_provinces) return true;
        if (!$this->provinces) return false;
        return in_array($provinceName, $this->provinces);
    }
}
