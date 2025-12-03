<?php

namespace App\Models;

use App\Enums\TransactionStatus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'transaction_number',
        'payment_method',
        'amount',
        'currency',
        'status',
        'gateway_transaction_id',
        'gateway_response',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => TransactionStatus::class,
        'amount' => 'decimal:2',
        'gateway_response' => 'json',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the order relationship.
     */
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
}
