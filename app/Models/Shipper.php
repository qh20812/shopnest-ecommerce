<?php
// app/Models/Shipper.php

namespace App\Models;

use App\Enums\ShipperStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipper extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'province_id',
        'vehicle_type',
        'license_plate',
        'status',
        'rating',
        'total_deliveries',
        'completed_deliveries'
    ];

    protected $casts = [
        'status' => ShipperStatus::class,
        'rating' => 'decimal:2',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ShipperReview::class);
    }

    public function credibility()
    {
        return $this->hasOne(ShipperCredibility::class);
    }

    public function province()
    {
        return $this->belongsTo(AdministrativeDivision::class, 'province_id');
    }

    public function getSuccessRateAttribute(): float
    {
        return $this->total_deliveries > 0
            ? round(($this->completed_deliveries / $this->total_deliveries) * 100, 2)
            : 0;
    }
}
