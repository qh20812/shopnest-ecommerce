<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'permission_name',
        'description',
    ];

    /**
     * Get the roles relationship.
     */
    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class, 'permission_role');
    }
}
