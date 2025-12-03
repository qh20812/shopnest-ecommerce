<?php

namespace App\Models;

use App\Enums\TwoFactorChallengeMethod;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorChallenge extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'two_factor_challenges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'two_factor_authentication_id',
        'code',
        'expires_at',
        'verified_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'method' => TwoFactorChallengeMethod::class,
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the twoFactorAuthentication relationship.
     */
    public function twoFactorAuthentication()
    {
        return $this->belongsTo(\App\Models\TwoFactorAuthentication::class);
    }
}
