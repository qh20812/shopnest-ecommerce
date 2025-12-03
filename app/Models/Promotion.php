<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promotions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shop_id',
        'promotion_name',
        'description',
        'promotion_type',
        'discount_value',
        'min_order_value',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'customer_eligibility',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'customer_eligibility' => 'json',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the shop relationship.
     */
    public function shop()
    {
        return $this->belongsTo(\App\Models\Shop::class);
    }

    /**
     * Get the codes relationship.
     */
    public function codes()
    {
        return $this->hasMany(\App\Models\PromotionCode::class);
    }

    /**
     * Get the orders relationship.
     */
    public function orders()
    {
        return $this->belongsToMany(\App\Models\Order::class, 'order_promotion');
    }
}
