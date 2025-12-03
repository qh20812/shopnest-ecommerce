<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionCode extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promotion_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'promotion_id',
        'code',
        'usage_limit',
        'used_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    /**
     * Get the promotion relationship.
     */
    public function promotion()
    {
        return $this->belongsTo(\App\Models\Promotion::class);
    }
}
