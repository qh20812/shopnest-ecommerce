<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country_name',
        'iso_code_2',
        'iso_code_3',
        'phone_code',
        'currency',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the administrativeDivisions relationship.
     */
    public function administrativeDivisions()
    {
        return $this->hasMany(\App\Models\AdministrativeDivision::class);
    }

    /**
     * Get the userAddresses relationship.
     */
    public function userAddresses()
    {
        return $this->hasMany(\App\Models\UserAddress::class);
    }
}
