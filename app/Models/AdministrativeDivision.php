<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdministrativeDivision extends Model
{
    protected $fillable = ['country_id', 'parent_id', 'name', 'level', 'code'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'division_id');
    }
}
