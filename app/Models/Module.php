<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'order',
        'icon',
        'route',
        'parent_id',
        'is_active',
    ];

    // Modules <-> Roles (many-to-many)
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permissions')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    // Module -> Actions (one-to-many)
    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    // Optional: parent/children structure
    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id');
    }
}
