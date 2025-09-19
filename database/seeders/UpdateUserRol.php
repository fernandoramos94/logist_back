<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UpdateUserRol extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = User::all();
        foreach ($rows as $row) {
            $row->role_id = (int)$row->type_admin;
            $row->save();
        }
    }
}
