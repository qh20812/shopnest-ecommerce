<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'phone_number',
        'password',
        'full_name',
        'date_of_birth',
        'gender',
        'avatar_url',
        'bio',
        'default_address_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
    ];

    /**
     * Get the roles relationship.
     */
    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class, 'role_user');
    }

    /**
     * Get the addresses relationship.
     */
    public function addresses()
    {
        return $this->hasMany(\App\Models\UserAddress::class);
    }

    /**
     * Get the defaultAddress relationship.
     */
    public function defaultAddress()
    {
        return $this->belongsTo(\App\Models\UserAddress::class, 'default_address_id');
    }

    /**
     * Get the shops relationship.
     */
    public function shops()
    {
        return $this->hasMany(\App\Models\Shop::class, 'owner_id');
    }

    /**
     * Get the orders relationship.
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'customer_id');
    }

    /**
     * Get the cartItems relationship.
     */
    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class);
    }

    /**
     * Get the wishlists relationship.
     */
    public function wishlists()
    {
        return $this->hasMany(\App\Models\Wishlist::class);
    }

    /**
     * Get the reviews relationship.
     */
    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    /**
     * Get the notifications relationship.
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * Get the twoFactorMethods relationship.
     */
    public function twoFactorMethods()
    {
        return $this->hasMany(\App\Models\TwoFactorAuthentication::class);
    }
}
