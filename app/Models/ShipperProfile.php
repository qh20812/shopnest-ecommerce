<?php

namespace App\Models;

use App\Enums\VehicleType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipperProfile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipper_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_number',
        'license_number',
        'current_hub_id',
        'rating',
        'total_deliveries',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vehicle_type' => VehicleType::class,
        'rating' => 'decimal:2',
        'total_deliveries' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user relationship.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the currentHub relationship.
     */
    public function currentHub()
    {
        return $this->belongsTo(\App\Models\Hub::class, 'current_hub_id');
    }

    /**
     * Get the shippingDetails relationship.
     */
    public function shippingDetails()
    {
        return $this->hasMany(\App\Models\ShippingDetail::class, 'shipper_id');
    }

    /**
     * Get the ratings relationship.
     */
    public function ratings()
    {
        return $this->hasMany(\App\Models\ShipperRating::class);
    }
}
