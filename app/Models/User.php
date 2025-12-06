<?php

namespace App\Models;

use App\Enums\Gender;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
        'gender' => Gender::class,
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
        return $this->belongsToMany(Role::class, 'role_user')
            ->using(RoleUser::class)
            ->withTimestamps();
    }

    /**
     * Get the addresses relationship.
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Get the defaultAddress relationship.
     */
    public function defaultAddress()
    {
        return $this->belongsTo(UserAddress::class, 'default_address_id');
    }

    /**
     * Get the shops relationship.
     */
    public function shops()
    {
        return $this->hasMany(Shop::class, 'owner_id');
    }

    /**
     * Get the orders relationship.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get the cartItems relationship.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the wishlists relationship.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the reviews relationship.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the notifications relationship.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the twoFactorMethods relationship.
     */
    public function twoFactorMethods()
    {
        return $this->hasMany(TwoFactorAuthentication::class);
    }

    /**
     * Check if user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('role_name', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('role_name', $roles)->exists();
    }

    /**
     * Check if user has all of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('role_name', $roles)->count() === count($roles);
    }

    /**
     * Check if user is a customer.
     *
     * @return bool
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    /**
     * Check if user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a seller.
     *
     * @return bool
     */
    public function isSeller(): bool
    {
        return $this->hasRole('seller');
    }

    /**
     * Check if user is a shipper.
     *
     * @return bool
     */
    public function isShipper(): bool
    {
        return $this->hasRole('shipper');
    }
}
