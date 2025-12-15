<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'variant_id',
        'image_url',
        'thumbnail_url',
        'alt_text',
        'display_order',
        'is_primary',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    /**
     * Get the product relationship.
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    /**
     * Get the variant relationship (for variant-specific images).
     */
    public function variant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class, 'variant_id');
    }

    /**
     * Scope: Get only product-level images (not variant-specific).
     */
    public function scopeProductOnly($query)
    {
        return $query->whereNull('variant_id');
    }

    /**
     * Scope: Get only variant-specific images.
     */
    public function scopeVariantOnly($query)
    {
        return $query->whereNotNull('variant_id');
    }
}
