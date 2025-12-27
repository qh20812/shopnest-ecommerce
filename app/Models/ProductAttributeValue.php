<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_attribute_values';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'attribute_id',
        'value',
        'attribute_option_id',
    ];

    /**
     * Get the product that owns this attribute value.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute definition.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the attribute option if this is a select type.
     */
    public function attributeOption(): BelongsTo
    {
        return $this->belongsTo(AttributeOption::class);
    }

    /**
     * Get display value.
     */
    public function getDisplayValueAttribute(): string
    {
        if ($this->attributeOption) {
            return $this->attributeOption->display_label;
        }
        return $this->value;
    }
}
