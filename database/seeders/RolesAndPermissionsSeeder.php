<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar caché de Spatie (importante)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Crear roles si no existen (con guard 'web' explícito)
        $roles = ['super_admin', 'admin', 'customer', 'user'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }

        $this->command->info('Roles creados/verificados: ' . implode(', ', $roles));

        // 3. Asignar rol al usuario admin (si existe)
        $user = User::where('email', 'admin@admin.com')->first();
        $role = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();

        if ($user && $role) {
            // syncRoles reemplaza roles previos sin duplicar
            $user->syncRoles([$role->name]);
            $this->command->info("Rol '{$role->name}' asignado a {$user->name}.");
        } else {
            $this->command->warn('Usuario admin@admin.com o rol super_admin no encontrado. Se omitió la asignación.');
        }
    }
}
