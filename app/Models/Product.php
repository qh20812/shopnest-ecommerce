<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $primaryKey = 'product_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'thumbnail',
        'images',
        'base_price',
        'has_variants',
        'stock_quantity',
        'sold_count',
        'status',
        'is_featured',
        'published_at'
    ];

    protected $casts = [
        'images' => 'array',
        'base_price' => 'decimal:2',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'has_variants' => 'boolean',
    ];

    // Variant của sản phẩm
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    // Danh mục (many-to-many)
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    // Lấy giá thấp nhất từ variant
    public function getMinPriceAttribute(): ?float
    {
        if (!$this->has_variants) {
            return $this->base_price;
        }

        return $this->variants()->min('price');
    }

    // Lấy giá cao nhất
    public function getMaxPriceAttribute(): ?float
    {
        if (!$this->has_variants) {
            return $this->base_price;
        }

        return $this->variants()->max('price');
    }

    // Scope: sản phẩm đang bán
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    // Scope: sản phẩm nổi bật
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    // Lấy variant mặc định (khi khách vào trang chi tiết)
    public function getDefaultVariantAttribute(): ?ProductVariant
    {
        return $this->variants()->where('is_default', true)->first()
            ?? $this->variants()->active()->inStock()->first();
    }
}
