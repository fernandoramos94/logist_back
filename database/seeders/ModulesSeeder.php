<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('modules')->insert([
            [
                'name' => 'Calendario',
                "icon" => "fa fa-home",
                "route" => "calendar",
                "order" => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Servicio',
                "icon" => "fa fa-home",
                "route" => "",
                "order" => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Servicios Unificados',
                "icon" => "fa fa-home",
                "route" => "services/list",
                "order" => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Clientes',
                "icon" => "fa fa-home",
                "route" => "clients/list",
                "order" => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Conductores',
                "icon" => "fa fa-home",
                "route" => "driver/list",
                "order" => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Auxiliares',
                "icon" => "fa fa-home",
                "route" => "assistant/list",
                "order" => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Unidades',
                "icon" => "fa fa-home",
                "route" => "unit/list",
                "order" => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Cuentas de usuarios',
                "icon" => "fa fa-home",
                "route" => "account",
                "order" => 7,
                'is_active' => true,
            ],
        ]);
    }
}
