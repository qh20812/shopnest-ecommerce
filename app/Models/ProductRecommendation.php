<?php
// app/Models/ProductRecommendation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRecommendation extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'variant_id',
        'score',
        'source',
        'metadata',
        'expires_at'
    ];

    protected $casts = [
        'score' => 'decimal:4',
        'metadata' => 'array',
        'calculated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Scope: Gợi ý còn hiệu lực
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    // Scope: Gợi ý cá nhân hóa
    public function scopePersonalized($query, $userId)
    {
        return $query->where('user_id', $userId)->valid()->orderByDesc('score');
    }
}
