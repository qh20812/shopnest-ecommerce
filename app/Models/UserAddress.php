<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address_label',
        'recipient_name',
        'phone_number',
        'address_line1',
        'address_line2',
        'country_id',
        'province_id',
        'district_id',
        'ward_id',
        'postal_code',
        'latitude',
        'longitude',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the user relationship.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the country relationship.
     */
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    /**
     * Get the province relationship.
     */
    public function province()
    {
        return $this->belongsTo(\App\Models\AdministrativeDivision::class, 'province_id');
    }

    /**
     * Get the district relationship.
     */
    public function district()
    {
        return $this->belongsTo(\App\Models\AdministrativeDivision::class, 'district_id');
    }

    /**
     * Get the ward relationship.
     */
    public function ward()
    {
        return $this->belongsTo(\App\Models\AdministrativeDivision::class, 'ward_id');
    }
}
