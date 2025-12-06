<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_name',
        'description',
    ];

    /**
     * Get the users relationship.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->using(RoleUser::class)
            ->withTimestamps();
    }

    /**
     * Get the permissions relationship.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->using(PermissionRole::class)
            ->withTimestamps();
    }
}
