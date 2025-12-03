<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wishlist_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wishlist_id',
        'product_id',
    ];

    /**
     * Get the wishlist relationship.
     */
    public function wishlist()
    {
        return $this->belongsTo(\App\Models\Wishlist::class);
    }

    /**
     * Get the product relationship.
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
