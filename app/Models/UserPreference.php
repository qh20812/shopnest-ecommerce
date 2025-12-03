<?php

namespace App\Models;

use App\Enums\Theme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_preferences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'preference_key',
        'preference_value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'theme' => Theme::class,
        'preference_value' => 'json',
    ];

    /**
     * Get the user relationship.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
