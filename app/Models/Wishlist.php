<?php
// app/Models/Wishlist.php

namespace App\Models;

use App\Enums\WishlistPrivacy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'privacy', 'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'privacy' => WishlistPrivacy::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    // Lấy wishlist mặc định của user
    public static function getDefaultForUser($userId)
    {
        return self::where('user_id', $userId)->where('is_default', true)->first()
               ?? self::create([
                   'user_id' => $userId,
                   'name' => 'Danh sách yêu thích',
                   'is_default' => true
               ]);
    }

    // Tự động tăng items_count
    protected static function booted()
    {
        static::deleting(function ($wishlist) {
            $wishlist->items()->delete();
        });
    }
}