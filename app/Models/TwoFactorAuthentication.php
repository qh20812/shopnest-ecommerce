<?php

namespace App\Models;

use App\Enums\TwoFactorMethod;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuthentication extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'two_factor_authentications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'method',
        'identifier',
        'secret',
        'backup_codes',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'method' => TwoFactorMethod::class,
        'backup_codes' => 'json',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user relationship.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the challenges relationship.
     */
    public function challenges()
    {
        return $this->hasMany(\App\Models\TwoFactorChallenge::class);
    }
}
