<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hubs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hub_name',
        'hub_code',
        'address',
        'ward_id',
        'latitude',
        'longitude',
        'capacity',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the ward relationship.
     */
    public function ward()
    {
        return $this->belongsTo(\App\Models\AdministrativeDivision::class, 'ward_id');
    }

    /**
     * Get the shipmentJourneys relationship.
     */
    public function shipmentJourneys()
    {
        return $this->hasMany(\App\Models\ShipmentJourney::class);
    }
}
