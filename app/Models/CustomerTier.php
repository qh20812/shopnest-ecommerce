<?php
// app/Models/CustomerTier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'min_points',
        'discount_rate',
        'badge_color',
        'priority',
        'is_active'
    ];

    protected $casts = [
        'min_points' => 'integer',
        'discount_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('priority');
    }
}
