<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    // Roles <-> Modules (many-to-many) via permissions pivot
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'permissions')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    // Roles <-> Actions (many-to-many) via action_permissions pivot
    public function actions()
    {
        return $this->belongsToMany(Action::class, 'action_permissions')
            ->withPivot('is_active')
            ->withTimestamps();
    }
}
