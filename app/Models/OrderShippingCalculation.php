<?php
// app/Models/OrderShippingCalculation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShippingCalculation extends Model
{
    protected $fillable = [
        'order_id',
        'rule_id',
        'subtotal_before_rule',
        'applied_shipping_fee',
        'original_shipping_fee',
        'saved_amount',
        'applied_rule_name',
        'note'
    ];

    protected $casts = [
        'subtotal_before_rule' => 'decimal:2',
        'applied_shipping_fee' => 'decimal:2',
        'original_shipping_fee' => 'decimal:2',
        'saved_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(ShippingFeeRule::class);
    }
}
