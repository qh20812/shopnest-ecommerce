<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_variants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'sku',
        'variant_name',
        'attribute_values',
        'price',
        'compare_at_price',
        'cost_per_item',
        'stock_quantity',
        'reserved_quantity',
        'weight_grams',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribute_values' => 'array',
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_per_item' => 'decimal:2',
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'weight_grams' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product relationship.
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    /**
     * Get the images relationship (multiple images per variant).
     */
    public function images()
    {
        return $this->hasMany(\App\Models\ProductImage::class, 'variant_id');
    }

    /**
     * Get the attributeValues relationship.
     */
    public function attributeValues()
    {
        return $this->belongsToMany(\App\Models\AttributeValue::class, 'attribute_value_product_variant');
    }

    /**
     * Get variant attribute values (new attribute system).
     */
    public function variantAttributeValues()
    {
        return $this->hasMany(ProductVariantAttributeValue::class);
    }

    /**
     * Get all attributes with their values for this variant.
     */
    public function getAttributesWithValuesAttribute()
    {
        return $this->variantAttributeValues()
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
     * Get the orderItems relationship.
     */
    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    /**
     * Get the cartItems relationship.
     */
    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class);
    }

    /**
     * Get the flashSaleProducts relationship.
     */
    public function flashSaleProducts()
    {
        return $this->hasMany(\App\Models\FlashSaleProduct::class);
    }
}
