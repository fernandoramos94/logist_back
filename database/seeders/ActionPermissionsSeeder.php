<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\Action;

class ActionPermissionsSeeder extends Seeder
{
    /**
     * For Operador: only 'Ver' action enabled per module.
     * Admin falls back to module permission (no action-specific overrides).
     */
    public function run(): void
    {
        $operadorId = DB::table('roles')->where('name', 'Administrador')->value('id');
        if (!$operadorId) {
            return; // No Operador role found
        }

        // Find 'Ver' actions per module
        $actions = Action::where('is_active', true)->where('name', 'Ver')->get(['id', 'module_id']);

        foreach ($actions as $action) {
            // Ensure action permission exists and is active
            $exists = DB::table('action_permissions')
                ->where('role_id', $operadorId)
                ->where('action_id', $action->id)
                ->exists();
            if (!$exists) {
                DB::table('action_permissions')->insert([
                    'role_id' => $operadorId,
                    'action_id' => $action->id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Optional: explicitly disable other actions for this module by inserting action_permissions with is_active=false
            $otherActions = Action::where('module_id', $action->module_id)
                ->where('name', '!=', 'Ver')
                ->pluck('id');

            foreach ($otherActions as $otherId) {
                $row = DB::table('action_permissions')
                    ->where('role_id', $operadorId)
                    ->where('action_id', $otherId)
                    ->first();
                if (!$row) {
                    DB::table('action_permissions')->insert([
                        'role_id' => $operadorId,
                        'action_id' => $otherId,
                        'is_active' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } elseif ($row && $row->is_active) {
                    DB::table('action_permissions')
                        ->where('id', $row->id)
                        ->update(['is_active' => false, 'updated_at' => now()]);
                }
            }
        }
    }
}
