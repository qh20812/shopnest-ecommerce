<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSaleProduct extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'flash_sale_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'flash_sale_event_id',
        'product_variant_id',
        'flash_price',
        'quantity_limit',
        'sold_quantity',
        'max_purchase_per_user',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'flash_price' => 'decimal:2',
        'quantity_limit' => 'integer',
        'sold_quantity' => 'integer',
        'max_purchase_per_user' => 'integer',
    ];

    /**
     * Get the flashSaleEvent relationship.
     */
    public function flashSaleEvent()
    {
        return $this->belongsTo(\App\Models\FlashSaleEvent::class);
    }

    /**
     * Get the productVariant relationship.
     */
    public function productVariant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class);
    }
}
