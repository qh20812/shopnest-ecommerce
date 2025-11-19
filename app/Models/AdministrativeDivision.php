<?php

namespace App\Models;

use App\Enums\AdministrativeDivisionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdministrativeDivision extends Model
{
    protected $fillable = ['country_id', 'name', 'type', 'code'];

    protected $casts = [
        'type' => AdministrativeDivisionType::class,
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'division_id');
    }
}
