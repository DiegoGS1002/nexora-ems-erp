<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'department',
        'code',
        'parent_role_id',
        'description',
        'is_active',
        'allow_assignment',
        'permissions',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'allow_assignment' => 'boolean',
        'permissions'      => 'array',
    ];

    public function parentRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'parent_role_id');
    }

    public function childRoles(): HasMany
    {
        return $this->hasMany(Role::class, 'parent_role_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employees::class, 'role', 'name');
    }

    public function getPermissionForModule(string $module, string $action): bool
    {
        $permissions = $this->permissions ?? [];

        if (isset($permissions[$module][$action]) && $permissions[$module][$action]) {
            return true;
        }

        if ($this->parent_role_id && $this->parentRole) {
            return $this->parentRole->getPermissionForModule($module, $action);
        }

        return false;
    }
}
