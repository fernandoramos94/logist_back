<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'Administrador',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Director',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Coordinador',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'name' => 'Supervisor',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'name' => 'Cliente',
                'is_active' => true,
            ],
        ]);
    }
}
