<?php

namespace App\Models;

use App\Enums\ShipmentJourneyStatus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentJourney extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipment_journeys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipping_detail_id',
        'hub_id',
        'status',
        'notes',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ShipmentJourneyStatus::class,
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the shippingDetail relationship.
     */
    public function shippingDetail()
    {
        return $this->belongsTo(\App\Models\ShippingDetail::class);
    }

    /**
     * Get the hub relationship.
     */
    public function hub()
    {
        return $this->belongsTo(\App\Models\Hub::class);
    }
}
