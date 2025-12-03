<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'sku',
        'quantity',
        'unit_price',
        'subtotal',
        'discount_amount',
        'total_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the order relationship.
     */
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    /**
     * Get the productVariant relationship.
     */
    public function productVariant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class);
    }

    /**
     * Get the review relationship.
     */
    public function review()
    {
        return $this->hasOne(\App\Models\Review::class);
    }

    /**
     * Get the returnItems relationship.
     */
    public function returnItems()
    {
        return $this->hasMany(\App\Models\ReturnItem::class);
    }
}
