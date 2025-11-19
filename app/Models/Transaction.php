<?php
// app/Models/Transaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'type',             // payment | refund
        'amount',
        'gateway',          // cod, stripe, momo, vnpay, paypal
        'gateway_transaction_id',
        'status'            // pending, completed, failed
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scope: Giao dịch thanh toán thành công
    public function scopePaymentSuccess($query)
    {
        return $query->where('type', 'payment')
                     ->where('status', 'completed');
    }

    // Scope: Hoàn tiền
    public function scopeRefund($query)
    {
        return $query->where('type', 'refund');
    }
}