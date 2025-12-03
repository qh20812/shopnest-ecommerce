<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalAddress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'international_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'country_id',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
    ];

    /**
     * Get the addressable relationship.
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * Get the country relationship.
     */
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }
}
