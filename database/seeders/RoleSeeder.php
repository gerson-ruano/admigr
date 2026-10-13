<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos antes de crear roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = ['super_admin', 'customer', 'admin', 'user'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
            $this->command->info("Rol '{$roleName}' listo.");
        }
    }
}
