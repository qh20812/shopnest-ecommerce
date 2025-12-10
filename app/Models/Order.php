<?php

namespace App\Models;

use App\Enums\PaymentMethod;

use App\Enums\PaymentStatus;

use App\Enums\OrderStatus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'shop_id',
        'status',
        'payment_status',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'tax_amount',
        'total_amount',
        'currency',
        'shipping_address_id',
        'payment_method',
        'note',
        'cancelled_reason',
        'cancelled_at',
        'confirmed_at',
        'delivered_at',
        'created_at', // Allow setting created_at for testing purposes
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
        'status' => OrderStatus::class,
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the customer relationship.
     */
    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }

    /**
     * Get the shop relationship.
     */
    public function shop()
    {
        return $this->belongsTo(\App\Models\Shop::class);
    }

    /**
     * Get the shippingAddress relationship.
     */
    public function shippingAddress()
    {
        return $this->belongsTo(\App\Models\UserAddress::class, 'shipping_address_id');
    }

    /**
     * Get the items relationship.
     */
    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    /**
     * Get the transactions relationship.
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    /**
     * Get the shippingDetails relationship.
     */
    public function shippingDetails()
    {
        return $this->hasOne(\App\Models\ShippingDetail::class);
    }

    /**
     * Get the promotions relationship.
     */
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'order_promotion')
            ->using(OrderPromotion::class)
            ->withPivot('discount_amount')
            ->withTimestamps();
    }

    /**
     * Get the returns relationship.
     */
    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    /**
     * Get the disputes relationship.
     */
    public function disputes()
    {
        return $this->hasMany(\App\Models\Dispute::class);
    }
}
