<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shops';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'shop_name',
        'slug',
        'description',
        'logo_url',
        'banner_url',
        'rating',
        'total_products',
        'total_followers',
        'total_orders',
        'response_rate',
        'response_time_hours',
        'is_verified',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'decimal:2',
        'response_rate' => 'decimal:2',
        'total_products' => 'integer',
        'total_followers' => 'integer',
        'total_orders' => 'integer',
        'response_time_hours' => 'integer',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the owner relationship.
     */
    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }

    /**
     * Get the products relationship.
     */
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    /**
     * Get the orders relationship.
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * Get the promotions relationship.
     */
    public function promotions()
    {
        return $this->hasMany(\App\Models\Promotion::class);
    }
}
