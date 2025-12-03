<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipping_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'shipper_id',
        'tracking_number',
        'carrier',
        'status',
        'estimated_delivery',
        'actual_delivery',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estimated_delivery' => 'datetime',
        'actual_delivery' => 'datetime',
    ];

    /**
     * Get the order relationship.
     */
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    /**
     * Get the shipper relationship.
     */
    public function shipper()
    {
        return $this->belongsTo(\App\Models\User::class, 'shipper_id');
    }

    /**
     * Get the journeys relationship.
     */
    public function journeys()
    {
        return $this->hasMany(\App\Models\ShipmentJourney::class);
    }
}
