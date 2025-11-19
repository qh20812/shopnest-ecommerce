<?php
// app/Models/ShipperReview.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipperReview extends Model
{
    protected $fillable = [
        'shipper_id',
        'order_id',
        'customer_id',
        'rating',
        'comment',
        'images',
        'is_anonymous',
        'is_hidden'
    ];

    protected $casts = [
        'images' => 'array',
        'is_anonymous' => 'boolean',
        'is_hidden' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
    