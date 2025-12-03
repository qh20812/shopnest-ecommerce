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
}
