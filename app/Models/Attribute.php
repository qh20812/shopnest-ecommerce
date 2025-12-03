<?php

namespace App\Models;

use App\Enums\AttributeInputType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'attribute_name',
        'display_name',
        'input_type',
        'is_required',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'input_type' => AttributeInputType::class,
        'is_required' => 'boolean',
    ];

    /**
     * Get the values relationship.
     */
    public function values()
    {
        return $this->hasMany(\App\Models\AttributeValue::class);
    }
}
