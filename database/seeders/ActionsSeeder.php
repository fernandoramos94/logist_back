<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Module;
use App\Models\Action;

class ActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultActions = [
            ['name' => 'Ver', 'icon' => 'fa fa-eye'],
            ['name' => 'Crear', 'icon' => 'fa fa-plus'],
            ['name' => 'Editar', 'icon' => 'fa fa-edit'],
            ['name' => 'Eliminar', 'icon' => 'fa fa-trash']
        ];

        $modules = Module::where('is_active', true)->orderBy('order')->get();
        $orderBase = 1;

        foreach ($modules as $module) {
            $order = $orderBase;
            foreach ($defaultActions as $def) {
                $exists = Action::where('module_id', $module->id)
                    ->where('name', $def['name'])
                    ->exists();
                if ($exists) {
                    continue;
                }

                Action::create([
                    'name' => $def['name'],
                    'order' => $order++,
                    'icon' => $def['icon'],
                    'module_id' => $module->id,
                    'is_active' => true,
                ]);
            }
        }
    }
}
