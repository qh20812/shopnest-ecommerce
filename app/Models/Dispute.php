<?php
// app/Models/Dispute.php

namespace App\Models;

use App\Enums\DisputeStatus;
use App\Enums\DisputeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispute extends Model
{
    protected $fillable = [
        'order_id', 'customer_id', 'created_by', 'title', 'description',
        'images', 'type', 'requested_refund_amount', 'status',
        'resolution_note', 'resolved_by'
    ];

    protected $casts = [
        'images' => 'array',
        'requested_refund_amount' => 'decimal:2',
        'resolved_at' => 'datetime',
        'type' => DisputeType::class,
        'status' => DisputeStatus::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}