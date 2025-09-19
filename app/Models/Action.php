<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'order',
        'icon',
        'is_active',
        'module_id',
    ];

    // Action -> Module (many-to-one)
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    // Actions <-> Roles (many-to-many) via action_permissions pivot
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'action_permissions')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Scope: filter actions permitted for a given role (module-level permission).
     */
    public function scopeForRole(Builder $query, int $roleId): Builder
    {
        return $query
            ->select('actions.*')
            ->join('modules', 'modules.id', '=', 'actions.module_id')
            ->join('permissions', 'permissions.module_id', '=', 'modules.id')
            ->leftJoin('action_permissions as ap', function ($join) use ($roleId) {
                $join->on('ap.action_id', '=', 'actions.id')
                     ->where('ap.role_id', '=', $roleId);
            })
            ->where('permissions.role_id', $roleId)
            ->where('modules.is_active', true)
            ->where('actions.is_active', true)
            ->where(function ($q) {
                $q->whereNotNull('ap.id')->where('ap.is_active', true)
                  ->orWhere(function ($q2) {
                      $q2->whereNull('ap.id')->where('permissions.is_active', true);
                  });
            })
            ->distinct();
    }

    /**
     * Scope: filter actions permitted for a given user via their role.
     * Note: user->role_id must be set.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $this->scopeForRole($query, (int) $user->role_id);
    }

    /**
     * Scope: restrict actions to a specific module ID.
     */
    public function scopeInModule(Builder $query, int $moduleId): Builder
    {
        return $query->where('actions.module_id', $moduleId);
    }

    /**
     * Scope: actions permitted for a role within a specific module.
     */
    public function scopeForRoleInModule(Builder $query, int $roleId, int $moduleId): Builder
    {
        return $this->scopeForRole($query, $roleId)->where('actions.module_id', $moduleId);
    }

    /**
     * Scope: actions permitted for a user within a specific module.
     */
    public function scopeForUserInModule(Builder $query, User $user, int $moduleId): Builder
    {
        return $this->scopeForUser($query, $user)->where('actions.module_id', $moduleId);
    }
}

