<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone_number',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Cho phép login bằng username, email hoặc phone_number
    public function getAuthIdentifierName()
    {
        return 'username'; // Laravel sẽ dùng cột này để tìm user
    }

    public function getAuthIdentifier()
    {
        return $this->username ?? $this->email ?? $this->phone_number;
    }

    // Relationships
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function shippingSubscriptions()
    {
        return $this->hasMany(UserShippingSubscription::class);
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class, 'customer_id');
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_user')
            ->withPivot('used_count', 'last_used_at')
            ->withTimestamps();
    }

    // Helper: Lấy địa chỉ mặc định
    public function defaultAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }

    // Helper: Tính tổng điểm loyalty hiện tại
    public function getCurrentLoyaltyPoints(): int
    {
        return $this->loyaltyPoints()->sum('points');
    }

    // Helper: Kiểm tra có gói freeship active không
    public function hasActiveFreeship(): bool
    {
        return $this->shippingSubscriptions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->exists();
    }
}
