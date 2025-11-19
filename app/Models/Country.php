<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = ['name', 'iso_code'];
    public $timestamps = true;

    public function divisions(): HasMany
    {
        return $this->hasMany(AdministrativeDivision::class);
    }
}
