<?php
// app/Models/UserAddress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'province_id',
        'ward_id',
        'street',
        'phone_number',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(AdministrativeDivision::class, 'province_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(AdministrativeDivision::class, 'ward_id');
    }

    // Địa chỉ đầy đủ để hiển thị
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->street,
            $this->ward?->name,
            $this->province?->name,
            'Việt Nam'
        ];

        return implode(', ', array_filter($parts));
    }

    // Scope: Địa chỉ mặc định
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
