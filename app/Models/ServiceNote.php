<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceNote extends Model
{
    use HasFactory;

    // Explicit table (migration created 'service_notes')
    protected $table = 'service_notes';

    // Allow mass assignment
    protected $fillable = [
        'service_id',
        'user_id',
        'note',
    ];
}
