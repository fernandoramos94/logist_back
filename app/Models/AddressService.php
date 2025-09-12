<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressService extends Model
{

    public $table = 'address_services';
    public $timestamps = true;
    use HasFactory;
}
