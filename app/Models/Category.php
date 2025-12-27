<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'category_name',
        'slug',
        'description',
        'image_url',
        'display_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the parent relationship.
     */
    public function parent()
    {
        return $this->belongsTo(\App\Models\Category::class, 'parent_id');
    }

    /**
     * Get the children relationship.
     */
    public function children()
    {
        return $this->hasMany(\App\Models\Category::class, 'parent_id');
    }

    /**
     * Get the products relationship.
     */
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    /**
     * Get the attributes associated with this category.
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'category_attribute')
            ->withPivot(['is_variant', 'is_required', 'is_filterable', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * Get variant attributes (attributes that create SKUs).
     */
    public function variantAttributes()
    {
        return $this->attributes()->wherePivot('is_variant', true);
    }

    /**
     * Get specification attributes (non-variant attributes).
     */
    public function specificationAttributes()
    {
        return $this->attributes()->wherePivot('is_variant', false);
    }
}
