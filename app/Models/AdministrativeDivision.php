<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrativeDivision extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'administrative_divisions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country_id',
        'parent_id',
        'division_name',
        'division_type',
        'code',
        'codename',
        'short_codename',
        'phone_code',
    ];

    /**
     * Get the country relationship.
     */
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    /**
     * Get the parent relationship.
     */
    public function parent()
    {
        return $this->belongsTo(\App\Models\AdministrativeDivision::class, 'parent_id');
    }

    /**
     * Get the children relationship.
     */
    public function children()
    {
        return $this->hasMany(\App\Models\AdministrativeDivision::class, 'parent_id');
    }

    /**
     * Get the wards relationship.
     */
    public function wards()
    {
        return $this->hasMany(\App\Models\AdministrativeDivision::class, 'parent_id')
            ->where('division_type', 'ward');
    }

    /**
     * Scope a query to only include provinces.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProvinces($query)
    {
        return $query->where('division_type', 'province');
    }

    /**
     * Scope a query to only include wards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWards($query)
    {
        return $query->where('division_type', 'ward');
    }
}
