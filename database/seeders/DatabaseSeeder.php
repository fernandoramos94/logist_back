<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->insert([[
            'name' => "Pendiente"
        ], [ "name" => "En Ruta" ], ["name"=>"En proceso Carga"], ["name" => "En Ruta a Entregar"], ["name" => "Finalizado"], ["name" => "Servicio Exitoso"]]);
    }
}
