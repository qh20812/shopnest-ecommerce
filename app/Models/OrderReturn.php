<?php
// app/Models/OrderReturn.php

namespace App\Models;

use App\Enums\OrderReturnStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    protected $table = 'order_returns'; // bắt buộc vì tên model không khớp

    protected $fillable = [
        'order_id',
        'order_item_id',
        'quantity',
        'refund_amount',
        'reason',
        'images',
        'status',
        'admin_note',
        'approved_by'
    ];

    protected $casts = [
        'images' => 'array',
        'refund_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'status' => OrderReturnStatus::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
