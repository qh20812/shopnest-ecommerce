<?php
// app/Models/Order.php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    // Lưu ý: Không dùng SoftDeletes cho Orders vì có thể gây nhầm lẫn với status
    // Nếu cần "xóa" đơn hàng, dùng status = 'cancelled'

    protected $fillable = [
        'order_number',
        'customer_id',
        'sub_total',
        'shipping_fee',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'shipping_address_id',
        'customer_phone',
        'promotion_id',
        'notes'
    ];

    protected $casts = [
        'sub_total' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
        'status' => OrderStatus::class,
    ];

    // Quan hệ
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function deliveryAssignment()
    {
        return $this->hasOne(DeliveryAssignment::class);
    }

    public function deliveryAssignments(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class);
    }

    public function shippingCalculation()
    {
        return $this->hasOne(OrderShippingCalculation::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    // Helper: Tính tổng tiền đã thanh toán
    public function getPaidAmountAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->sum('amount');
    }

    // Helper: Còn nợ bao nhiêu
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->getPaidAmountAttribute());
    }

    // Scope: Đơn đang giao
    public function scopeInDelivery($query)
    {
        return $query->where('status', 'shipped')
            ->orWhere('status', 'processing');
    }
}
