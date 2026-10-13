<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de Spatie antes de crear roles (evita errores raros)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear TODOS los roles con guard 'web' explícito
        $roles = ['super_admin', 'admin', 'seller', 'customer', 'employee'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }

        // 2. Recuperar los roles que vamos a asignar
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        $sellerRole     = Role::where('name', 'seller')->where('guard_name', 'web')->first();

        // 3. Crear usuario super admin
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'administrador',
                'phone'    => 12341234,
                'profile'  => 'admin',
                'status'   => 1,
                'image'    => 'miimagen.jpg',
                'tema'     => 0,
                'password' => bcrypt('12341234'),
            ]
        );
        // syncRoles en lugar de assignRole (idempotente, no duplica)
        $superadmin->syncRoles([$superAdminRole->name]);

        // 4. Crear usuario seller
        $seller = User::firstOrCreate(
            ['email' => 'seller@admin.com'],
            [
                'name'     => 'luis ramirez',
                'phone'    => 12341234,
                'profile'  => 'seller',
                'status'   => 1,
                'image'    => 'noimg.png',
                'tema'     => 0,
                'password' => bcrypt('12341234'),
            ]
        );
        $seller->syncRoles([$sellerRole->name]);

        // 5. Crear usuario employee (sin rol por ahora)
        User::firstOrCreate(
            ['email' => 'employee@admin.com'],
            [
                'name'     => 'juan perez',
                'phone'    => 12341234,
                'profile'  => 'employee',
                'status'   => 1,
                'image'    => 'noimg.png',
                'tema'     => 0,
                'password' => bcrypt('12341234'),
            ]
        );

        $this->command->info('Usuarios y roles creados correctamente.');
    }
}