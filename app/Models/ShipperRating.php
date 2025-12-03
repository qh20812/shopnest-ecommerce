<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipperRating extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipper_ratings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipper_id',
        'order_id',
        'rating',
        'comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Get the shipper relationship.
     */
    public function shipper()
    {
        return $this->belongsTo(\App\Models\User::class, 'shipper_id');
    }

    /**
     * Get the order relationship.
     */
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
}
