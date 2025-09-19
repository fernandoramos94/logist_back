<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Module;

class PermissionsSeeder extends Seeder
{
    /**
     * Grant all active modules to Admin and Operador roles (module-level permissions).
     */
    public function run(): void
    {
        // Resolve role IDs by name; skip seeding if roles are missing
        $adminId = DB::table('roles')->where('name', 'Administrador')->value('id');
        $operadorId = DB::table('roles')->where('name', 'Director')->value('id');

        $roleIds = array_filter([$adminId, $operadorId]);
        if (empty($roleIds)) {
            return; // No target roles found
        }

        $modules = Module::where('is_active', true)->pluck('id');

        foreach ($roleIds as $roleId) {
            foreach ($modules as $moduleId) {
                $exists = DB::table('permissions')
                    ->where('role_id', $roleId)
                    ->where('module_id', $moduleId)
                    ->exists();
                if ($exists) {
                    continue;
                }
                DB::table('permissions')->insert([
                    'role_id' => $roleId,
                    'module_id' => $moduleId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
