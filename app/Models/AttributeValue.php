<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attribute_values';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribute_id',
        'value',
        'display_value',
        'color_code',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_order' => 'integer',
    ];

    /**
     * Get the attribute relationship.
     */
    public function attribute()
    {
        return $this->belongsTo(\App\Models\Attribute::class);
    }

    /**
     * Get the productVariants relationship.
     */
    public function productVariants()
    {
        return $this->belongsToMany(\App\Models\ProductVariant::class, 'attribute_value_product_variant');
    }
}
