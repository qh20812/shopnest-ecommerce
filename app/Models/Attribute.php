<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attributes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'input_type',
        'description',
        'validation_rules',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'validation_rules' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attribute) {
            if (empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });
    }

    /**
     * Get the attribute options.
     */
    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class)->orderBy('sort_order');
    }

    /**
     * Get the categories that use this attribute.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_attribute')
            ->withPivot(['is_variant', 'is_required', 'is_filterable', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Get product attribute values.
     */
    public function productAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     * Get variant attribute values.
     */
    public function variantAttributeValues(): HasMany
    {
        return $this->hasMany(ProductVariantAttributeValue::class);
    }

    /**
     * Scope for active attributes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this is a select type attribute.
     */
    public function isSelectType(): bool
    {
        return $this->input_type === 'select';
    }
}
