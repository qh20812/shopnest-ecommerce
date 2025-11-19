<?php
// app/Models/UserShippingSubscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserShippingSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'order_id',
        'transaction_id',
        'starts_at',
        'expires_at',
        'is_active',
        'auto_renew',
        'used_count',
        'saved_amount'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'auto_renew' => 'boolean',
        'saved_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(ShippingPackage::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // Kiểm tra gói còn hiệu lực không
    public function isValid(): bool
    {
        return $this->is_active && $this->expires_at->gt(now());
    }

    // Scope: Gói còn hiệu lực
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>', now());
    }
}
