<?php
// app/Models/ShippingPackage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPackage extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_days',
        'is_active',
        'is_popular',
        'sort_order',
        'sold_count',
        'active_subscriptions'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(UserShippingSubscription::class, 'package_id');
    }

    public function getDurationTextAttribute(): string
    {
        if ($this->duration_days >= 9999) return 'Trọn đời';
        if ($this->duration_days == 365) return '1 năm';
        if ($this->duration_days == 30) return '1 tháng';
        return "{$this->duration_days} ngày";
    }
}
