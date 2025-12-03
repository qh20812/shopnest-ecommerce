<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'return_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'return_id',
        'order_item_id',
        'quantity',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the return relationship.
     */
    public function return()
    {
        return $this->belongsTo(\App\Models\Return::class);
    }

    /**
     * Get the orderItem relationship.
     */
    public function orderItem()
    {
        return $this->belongsTo(\App\Models\OrderItem::class);
    }
}
