<?php
// app/Models/Shipper.php

namespace App\Models;

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

    public function assignments(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class);
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
