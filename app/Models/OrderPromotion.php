<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderPromotion extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_promotion';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'promotion_id',
        'discount_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order relationship.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the promotion relationship.
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
