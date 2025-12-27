<?php

namespace App\Models;

use App\Enums\ProductStatus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shop_id',
        'category_id',
        'brand_id',
        'seller_id',
        'product_name',
        'slug',
        'description',
        'specifications',
        'base_price',
        'currency',
        'weight_grams',
        'length_cm',
        'width_cm',
        'height_cm',
        'status',
        'total_quantity',
        'total_sold',
        'rating',
        'review_count',
        'view_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ProductStatus::class,
        'specifications' => 'json',
        'base_price' => 'decimal:2',
        'weight_grams' => 'integer',
        'length_cm' => 'integer',
        'width_cm' => 'integer',
        'height_cm' => 'integer',
        'total_quantity' => 'integer',
        'total_sold' => 'integer',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'view_count' => 'integer',
    ];

    /**
     * Get the shop relationship.
     */
    public function shop()
    {
        return $this->belongsTo(\App\Models\Shop::class);
    }

    /**
     * Get the category relationship.
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    /**
     * Get the brand relationship.
     */
    public function brand()
    {
        return $this->belongsTo(\App\Models\Brand::class);
    }

    /**
     * Get the seller relationship.
     */
    public function seller()
    {
        return $this->belongsTo(\App\Models\User::class, 'seller_id');
    }

    /**
     * Get the images relationship.
     */
    public function images()
    {
        return $this->hasMany(\App\Models\ProductImage::class);
    }

    /**
     * Get the variants relationship.
     */
    public function variants()
    {
        return $this->hasMany(\App\Models\ProductVariant::class);
    }

    /**
     * Get product attribute values (non-variant attributes/specs).
     */
    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     * Get all attributes with their values.
     */
    public function getAttributesWithValuesAttribute()
    {
        return $this->attributeValues()
            ->with(['attribute', 'attributeOption'])
            ->get()
            ->mapWithKeys(function ($attrValue) {
                return [
                    $attrValue->attribute->slug => [
                        'name' => $attrValue->attribute->name,
                        'value' => $attrValue->display_value,
                        'input_type' => $attrValue->attribute->input_type,
                    ]
                ];
            });
    }

    /**
     * Get the reviews relationship.
     */
    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    /**
     * Get the questions relationship.
     */
    public function questions()
    {
        return $this->hasMany(\App\Models\ProductQuestion::class);
    }

    /**
     * Get the views relationship.
     */
    public function views()
    {
        return $this->hasMany(\App\Models\ProductView::class);
    }

    /**
     * Get the wishlistItems relationship.
     */
    public function wishlistItems()
    {
        return $this->hasMany(\App\Models\WishlistItem::class);
    }
}
